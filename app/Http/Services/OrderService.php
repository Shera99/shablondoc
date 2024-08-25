<?php

namespace App\Http\Services;

use App\Enums\OrderStatus;
use App\Http\Modules\FileHandler;
use App\Models\Employee;
use App\Models\Order;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Http\Request;

class OrderService
{
    protected Order $order;

    public function __construct()
    {
        $this->order = app(Order::class);
    }

    /**
     * @throws \Exception
     */
    public function create(array $request_data, Request $request)
    {
//        dd($request->file('files'));
        $this->order->phone_number = $request_data['phone_number'];
        $this->order->delivery_date = $request_data['delivery_date'];

        if (!empty($request_data['template_id'])) $this->order->template_id = $request_data['template_id'];
        elseif (isset($request_data['document_name']) && !empty($request_data['document_name'])) {
            $this->order->document_name = $request_data['document_name'];
            $this->order->country_id = $request_data['country_id'];
            $this->order->language_id = $request_data['language_id'];
        } else {
            return ['error' => Response::HTTP_BAD_REQUEST, 'message' => 'Document data is required.'];
        }

        if ($request->hasFile('files')) {
            $files = $request->file('files');
            $paths = [];

            foreach ($files as $key => $file) {
                try {
                    $result = FileHandler::save($file, 'order');
                    $paths[] = $result['storedFilePath'];
                } catch (\Exception $e) {
                    return response()->json(['success' => false, 'errors' => ['message' => $e->getMessage()]])
                        ->setStatusCode(Response::HTTP_BAD_REQUEST);
                }
            }

            $this->order->document_file = $paths;
        } else {
            return ['error' => Response::HTTP_BAD_REQUEST, 'message' => 'Document(s) is required.'];
        }

        if (!empty($request_data['email'])) $this->order->email = $request_data['email'];
        if (!empty($request_data['comment'])) $this->order->comment = $request_data['comment'];
        if (!empty($request_data['mynumer'])) $this->order->mynumer = $request_data['mynumer'];
        if (!empty($request_data['address_id'])) $this->order->company_address_id = $request_data['address_id'];
        if (!empty($request_data['user_id'])) $this->order->user_id = $request_data['user_id'];

        $this->order->status = !empty($request_data['user_id']) ? OrderStatus::COMPLETED : OrderStatus::PENDING;
        $this->order->save();

        return $this->order;
    }

    public function list(Request $request, array $statuses, string $type): array
    {
        $query = DB::table('orders as o')
            ->join('payments as p', 'o.id', '=', 'p.foreign_id')
            ->join('templates as t', 'o.template_id', '=', 't.id')
            ->leftJoin('company_addresses as c_a', 'o.company_address_id', '=', 'c_a.id')
            ->leftJoin('companies as cm', 'c_a.company_id', '=', 'cm.id')
            ->leftJoin('countries as c', 'o.country_id', '=', 'c.id')
            ->leftJoin('users as u', 'o.user_id', '=', 'u.id')
            ->where('p.type', 'order')->whereIn('o.status', $statuses)
            ->where('p.status', 'completed');

        $query->when(function($q) {
            return DB::raw('o.language_id IS NOT NULL');
        }, function($q) {
            $q->leftJoin('languages as l', 'o.language_id', '=', 'l.id');
        });

        $query->when(function($q) {
            return DB::raw('o.language_id IS NULL AND o.template_id IS NOT NULL');
        }, function($q) {
            $q->leftJoin('translation_directions as td', 't.translation_direction_id', '=', 'td.id')
                ->leftJoin('languages as ld', 'td.target_language_id', '=', 'ld.id');
        });

        if ($type != 'completed') {
            if ($type == 'translated' && auth()->user()->hasRole('Employee'))
                $query->where('o.user_id', auth()->user()->id);
            else {
                if ($type == 'delivered' && auth()->user()->hasRole('Employee'))
                    $companies = Employee::where('user_id', auth()->user()->id)->pluck('company_id');
                else $companies = auth()->user()->companies->pluck('id');

                $company_addresses = DB::table('company_addresses')
                    ->whereIn('company_id', $companies)
                    ->pluck('id');
                $query = $query->where(function ($query) use ($company_addresses) {
                    return $query->whereIn('o.company_address_id', $company_addresses)
                        ->orWhere('o.user_id', auth()->user()->id);
                });
            }
        }

        if ($request->get('search')) {
            $search_text = '%' . $request->get('search') . '%';
            $query = $query->where(function ($query) use($search_text) {
                return $query->where('c_a.name', 'LIKE', $search_text)
                    ->orWhere('o.document_name', 'LIKE', $search_text)
                    ->orWhere('t.name', 'LIKE', $search_text)
                    ->orWhere('o.email', 'LIKE', $search_text)
                    ->orWhere('o.phone_number', 'LIKE', $search_text);
            });
        }

        if ($request->get('filters')) {
            $filter_array = (array)json_decode($request->get('filters'));

            if (isset($filter_array['by_date']) && !empty($filter_array['by_date'])) {
                $filter_data = explode('-', $filter_array['by_date']);
                $startDate = Carbon::createFromFormat('Y.m.d', $filter_data[0])->startOfDay()->format('Y-m-d H:i:s');
                $endDate = Carbon::createFromFormat('Y.m.d', $filter_data[1])->endOfDay()->format('Y-m-d H:i:s');
                $query = $query->whereBetween('o.created_at', [$startDate, $endDate]);
            } else if (isset($filter_array['by_employee']) && !empty($filter_array['by_employee'])) {
                $query = $query->where('o.user_id', $filter_array['by_employee']);
            } else if (isset($filter_array['by_document_type']) && !empty($filter_array['by_document_type'])) {
                $query = $query->where('t.document_type_id', $filter_array['by_document_type']);
            } else if (isset($filter_array['by_company']) && !empty($filter_array['by_company'])) {
                $query = $query->where('cm.id', $filter_array['by_company']);
            }
        }

        $query = $query->orderBy('o.id', 'desc');

        return $query->select(
            'o.id', 'o.user_id', 'o.template_id', 'o.template_data_id', 'o.company_address_id', 'o.country_id',
            'o.language_id', 'o.document_name', 'o.document_file', 'o.email', 'o.phone_number', 'o.delivery_date',
            'o.comment', 'o.status', 'o.created_at', 'o.print_date', 'o.updated_at',
            'c_a.name as company_address_name', 't.name as template_name', 'cm.id as company_id', 'cm.name as company_name',
            'c.name as country_name', 'l.name as language_name', 'l.name_en as language_name_en', 'u.login as translator_login',
            'u.name as translator_name', 'u.last_name as translator_last_name', 'ld.name as l_language_name', 'ld.name_en as l_language_name_en',
        )->paginate(15)->toArray();
    }
}
