<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Transaction;
use App\Http\Response\GlobalResponse;
use App\Models\Detail_transaction;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\DB;

class TransactionController extends Controller
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
            $perPage = $request->input('per_page', Transaction::count());
            $page = $request->input('page', 1);

            $skip = ($page - 1) * $perPage;

            $query = Transaction::query()
                ->join('outpatients', 'transactions.id_outpatient', '=', 'outpatients.id')
                ->select('transactions.*', 'outpatients.no_registration', 'outpatients.patient_name');

            if (!empty($search)) {
                $query->where(function ($q) use ($search) {
                    $q->where('transactions.date', 'like', "%{$search}%")
                        ->orWhere('outpatients.no_registration', 'like', "%{$search}%")
                        ->orWhere('outpatients.patient_name', 'like', "%{$search}%");
                });
            }

            $data = $query->with(['detail_transaction', 'outpatient'])->orderBy('transactions.id', 'desc')
                ->skip($skip)
                ->take($perPage)
                ->get();

            return GlobalResponse::jsonResponse(
                $data,
                200,
                'success',
                'Transaksi berhasil diambil'
            );
        } catch (\Throwable $e) {
            return GlobalResponse::jsonResponse(null, 500, 'error', $e->getMessage());
        } catch (\Exception $e) {
            return GlobalResponse::jsonResponse(null, 500, 'error', $e->getMessage());
        }
    }

    public function show($id)
    {
        try {
            $data = Transaction::with(['detail_transaction', 'outpatient'])->find($id);
            return GlobalResponse::jsonResponse(
                $data,
                200,
                'success',
                'Transaksi berhasil diambil'
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
                'id_outpatient'     => 'required|numeric',
            ]);

            //create post
            $data = Transaction::create([
                'id_outpatient'     => $request->id_outpatient,
                'date'     => date("Y-m-d"),
                'payment_methode'     => NULL,
                'total_transaction'     => 0,
                'remaining_payment'     => 0,
                'amount' => 0,
                'return_amount'     => 0,
                'payment_status'     => 'Belum Lunas',
            ]);

            $data->load(['detail_transaction', 'outpatient']);

            //return response
            return GlobalResponse::jsonResponse($data, 201, 'success', 'Transaksi berhasil ditambahkan!');
        } catch (\Throwable $e) {
            return GlobalResponse::jsonResponse(null, 500, 'error', $e->getMessage());
        } catch (\Exception $e) {
            return GlobalResponse::jsonResponse(null, 500, 'error', $e->getMessage());
        }
    }

    public function process(Request $request, $id)
    {
        try {
            //define validation rules
            $validator = $request->validate([
                'payment_methode'     => 'required',
                'amount' => 'required|numeric',
            ]);

            //find post by ID
            $data = Transaction::find($id);
            $detailtransaksi = Detail_transaction::where('id_transaction', $data['id'])->get();

            $remaining_payment = ($data['remaining_payment']) - $request->amount;
            if ($request->amount < $data['remaining_payment']) {
                return GlobalResponse::jsonResponse($data['remaining_payment'], 422, 'error', 'Transaksi tidak dapat diproses karena uang yang dibayarkan tidak sama dengan sisa bayar!');
            } else if ($data['total_transaction'] == 0) {
                return GlobalResponse::jsonResponse(null, 422, 'error', 'Detail transaksi kosong');
            } else {
                $data->update([
                    'payment_methode' => $request->payment_methode,
                    'remaining_payment' => 0,
                    'amount' => $request->amount,
                    'return_amount' => abs($remaining_payment),
                    'payment_status' => 'Lunas'
                ]);
                $data->load(['detail_transaction', 'outpatient']);
                return GlobalResponse::jsonResponse($data, 200, 'success', 'Transaksi berhasil diproses!');
            }
        } catch (\Throwable $e) {
            return GlobalResponse::jsonResponse(null, 500, 'error', $e->getMessage());
        } catch (\Exception $e) {
            return GlobalResponse::jsonResponse(null, 500, 'error', $e->getMessage());
        }
    }

    public function update(Request $request, $id)
    {
        try {
            //define validation rules
            $validator = $request->validate([
                'id_outpatient'     => 'required|numeric',
            ]);

            //find post by ID
            $data = Transaction::find($id);

            $data->update([
                'id_outpatient' => $request->id_outpatient,
            ]);
            $data->load(['detail_transaction', 'outpatient']);
            return GlobalResponse::jsonResponse($data, 201, 'success', 'Transaksi berhasil diubah!');
        } catch (\Throwable $e) {
            return GlobalResponse::jsonResponse(null, 500, 'error', $e->getMessage());
        } catch (\Exception $e) {
            return GlobalResponse::jsonResponse(null, 500, 'error', $e->getMessage());
        }
    }

    public function exportPDF($id)
    {
        try {
            //find post by ID
            $transaction = Transaction::with(['detail_transaction', 'outpatient'])->find($id);
            if ($transaction['payment_status'] == 'Lunas') {

                $initdetailtransaksi = Detail_transaction::with(['service'])->where('id_transaction', $id);

                $detailtransaksi = $initdetailtransaksi->get();

                $totalprice =  $initdetailtransaksi->sum('total_price');

                // Data to be passed to the view
                $data = [
                    'transaction' => $transaction,
                    'detailtransaksi' => $detailtransaksi,
                    'totalprice' => $totalprice
                ];

                // Load the view and pass the data
                $pdf = Pdf::loadView('pdf.document', $data);

                $customPaper = [0, 0, 204, 650]; // Width and height in mm (example: A6 size in mm)
                $pdf->setPaper($customPaper);

                $pdf->set_option('dpi', 72);

                // Download the PDF file
                return $pdf->stream('document.pdf');
            } else {
                return GlobalResponse::jsonResponse(null, 402, 'error', 'Transaksi belum lunas. Selesaikan transaksi!');
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
            $data = Transaction::find($id);
            $data->delete();
            DB::statement('alter table transactions auto_increment = 1');
            DB::statement('alter table detail_transactions auto_increment = 1');
            //return response
            return GlobalResponse::jsonResponse(null, 200, 'success', 'Transaksi dengan ID ' . $id . ' Berhasil Dihapus!');
        } catch (\Throwable $e) {
            return GlobalResponse::jsonResponse(null, 500, 'error', $e->getMessage());
        } catch (\Exception $e) {
            return GlobalResponse::jsonResponse(null, 500, 'error', $e->getMessage());
        }
    }
}
