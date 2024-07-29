<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Transaction;
use App\Models\Detail_transaction;
use App\Models\Service;
use App\Http\Response\GlobalResponse;
use Illuminate\Support\Facades\DB;

class DetailTransactionController extends Controller
{
    /**
     * index
     *
     * @return void
     */
    public function index(Request $request)
    {
        try {
            $search = $request->input('search', '');
            $perPage = $request->input('per_page', Detail_transaction::count());
            $page = $request->input('page', 1);

            $skip = ($page - 1) * $perPage;

            $query = Detail_transaction::query();

            if (!empty($search)) {
                $query->where('time', 'like', "%{$search}%");
            }

            //get all posts
            // $data = Service::with('detail_transaction')->latest()->paginate(5);
            $data = $query->with(['service', 'transaction'])->orderBy('id', 'desc')
                ->skip($skip)
                ->take($perPage)
                ->get();

            //return collection of data as a resource
            // return new Resources(true, 'List Layanan', $posts);
            return GlobalResponse::jsonResponse(
                $data,
                200,
                'success',
                'Detail Transaksi berhasil diambil'
            );
        } catch (\Throwable $e) {
            return GlobalResponse::jsonResponse(null, 500, 'error', $e->getMessage());
        } catch (\Exception $e) {
            return GlobalResponse::jsonResponse(null, 500, 'error', $e->getMessage());
        }
    }

    /**
     * store
     *
     * @param  mixed $request
     * @return void
     */
    public function store(Request $request)
    {
        try {
            //define validation rules
            $validator = $request->validate([
                'id_service'     => 'required|numeric',
                'id_transaction'     => 'required|numeric',
                'quantity'     => 'required|numeric',
                'discount'     => 'required|numeric',
            ]);

            $service = Service::find($request->id_service);
            $transaction = Transaction::find($request->id_transaction);
            if ($transaction['payment_status'] == "Belum Lunas") {
                //create post
                $data = Detail_transaction::create([
                    'id_service'     => $request->id_service,
                    'id_transaction'     => $request->id_transaction,
                    'time'     => date('h:i:s'),
                    'subtotal' => $service['price'],
                    'quantity'     => $request->quantity,
                    'discount'     => $request->discount,
                    'total_price'     => ($service['price'] * $request->quantity) - $request->discount,
                ]);

                $totalprice = Detail_transaction::where('id_transaction', '=', $request->id_transaction)->sum('total_price');

                $transaction->update([
                    'total_transaction' => $totalprice,
                    'remaining_payment' => $totalprice,
                ]);

                $data->load(['service', 'transaction']);
                //return response
                return GlobalResponse::jsonResponse($data, 200, 'success', 'Detail transaksi berhasil ditambahkan');
            } else {
                return GlobalResponse::jsonResponse(null, 422, 'error', 'Detail transaksi tidak dapat ditambahkan karena transaksi sudah lunas');
            }
        } catch (\Throwable $e) {
            return GlobalResponse::jsonResponse(null, 500, 'error', $e->getMessage());
        } catch (\Exception $e) {
            return GlobalResponse::jsonResponse(null, 500, 'error', $e->getMessage());
        }
    }

    public function showByTransactionId($transactionid)
    {
        try {
            $detailtransaksi = Detail_transaction::with(['service', 'transaction'])->where('id_transaction', $transactionid)->get();
            if ($detailtransaksi) {
                return GlobalResponse::jsonResponse($detailtransaksi, 200, 'success', 'Detail transaksi berhasil diambil');
            } else {
                return GlobalResponse::jsonResponse(null, 404, 'error', 'Detail transaksi tidak ditemukan');
            }
        } catch (\Throwable $e) {
            return GlobalResponse::jsonResponse(null, 500, 'error', $e->getMessage());
        } catch (\Exception $e) {
            return GlobalResponse::jsonResponse(null, 500, 'error', $e->getMessage());
        }
    }

    /**
     * update
     *
     * @param  mixed $request
     * @param  mixed $id
     * @return void
     */
    public function update(Request $request, $id)
    {
        try {
            //define validation rules
            $validator = $request->validate([
                'id_service'     => 'required|numeric',
                'id_transaction'     => 'required|numeric',
                'quantity'     => 'required|numeric',
                'discount'     => 'required|numeric',
            ]);

            //find post by ID
            $data = Detail_transaction::find($id);

            $service = Service::find($request->id_service);
            $transaction = Transaction::find($request->id_transaction);
            if ($transaction['payment_status'] == "Belum Lunas") {
                //create post
                $data->update([
                    'id_service'     => $request->id_service,
                    'id_transaction'     => $request->id_transaction,
                    'time'     => $data['time'],
                    'subtotal' => $service['price'],
                    'quantity'     => $request->quantity,
                    'discount'     => $request->discount,
                    'total_price'     => ($service['price'] * $request->quantity) - $request->discount,
                ]);

                $totalprice = Detail_transaction::where('id_transaction', '=', $request->id_transaction)->sum('total_price');

                $transaction->update([
                    'total_transaction' => $totalprice,
                    'remaining_payment' => $totalprice,
                ]);

                $data->load(['service', 'transaction']);
                //return response
                return GlobalResponse::jsonResponse($data, 200, 'success', 'Detail transaksi berhasil diedit');
            } else {
                return GlobalResponse::jsonResponse(null, 422, 'error', 'Detail transaksi tidak dapat diedit karena transaksi sudah lunas');
            }
        } catch (\Throwable $e) {
            return GlobalResponse::jsonResponse(null, 500, 'error', $e->getMessage());
        } catch (\Exception $e) {
            return GlobalResponse::jsonResponse(null, 500, 'error', $e->getMessage());
        }
    }

    /**
     * destroy
     *
     * @param  mixed $id
     * @return void
     */
    public function destroy($id)
    {
        try {
            //find post by ID
            $data = Detail_transaction::find($id);
            $transaction = Transaction::find($data['id_transaction']);
            if ($transaction['payment_status'] == "Belum Lunas") {
                $data->delete();
                DB::statement('alter table detail_transactions auto_increment = 1');
                $discount = Detail_transaction::where('id_transaction', '=', $data['id_transaction'])->sum('discount');
                $totalprice = Detail_transaction::where('id_transaction', '=', $data['id_transaction'])->sum('total_price');
                $transaction->update([
                    'total_transaction' => $totalprice - $discount,
                    'remaining_payment' => $totalprice - $discount,
                ]);
                //return response
                return GlobalResponse::jsonResponse(null, 200, 'success', 'Detail transaksi berhasil dihapus');
            } else {
                return GlobalResponse::jsonResponse(null, 422, 'error', 'Detail transaksi tidak dapat dihapus karena transaksi sudah lunas');
            }
        } catch (\Throwable $e) {
            return GlobalResponse::jsonResponse(null, 500, 'error', $e->getMessage());
        } catch (\Exception $e) {
            return GlobalResponse::jsonResponse(null, 500, 'error', $e->getMessage());
        }
    }
}
