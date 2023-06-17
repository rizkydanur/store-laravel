<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use illuminate\Support\Facades\Auth;

use App\Models\ProductCart;
use App\Models\Transaction;
use App\Models\TransactionDetail;

use Exception;

use Midtrans\Snap;
use Midtrans\Config;
use Midtrans\Notification;


class CheckoutController extends Controller
{
    public function process(Request $request)
    {
        // Save users data
        $user = Auth::user();
        $user->update($request->except('total_price'));

        // Proses Checkout
        $code = 'STORE-' . mt_rand(000000, 999999);
        $carts = ProductCart::with(['product','user'])
                        ->where('users_id', Auth::user()->id)
                        ->get();
        
        // Transaction Create
        $transaction = Transaction::create([
            'users_id' => Auth::user()->id,
            'inscurance_id' => 0,
            'shipping_price' => 0,
            'total_price' => $request->total_price,
            'transaction_status' => 'PENDING',
            'code' => $code,
        ]);

        foreach ($carts as $cart) 
        {
            $trx = 'TRX-'. mt_rand(000000, 999999);

            TransactionDetail::create([
            'transactions_id' => $transaction->id,
            'products_id' => $cart->product->id,
            'price' => $cart->product->price,
            'shipping_status' => 'PENDING',
            'resi' => '',
            'code' => $trx,
            ]);
        }

        //delete cart data
        ProductCart::with('users_id', Auth::user()->id)->delete();

        // Konfigurasi midtrans
        // Set your Merchant Server Key
        Config::$serverKey = config('services.midtrans.serverKey');
        // Set to Development/Sandbox Environment (default). Set to true for Production Environment (accept real transaction).
        Config::$isProduction = config('services.midtrans.isProduction');
        // Set sanitization on (default)
        Config::$isSanitized = config('services.midtrans.isSanitized');
        // Set 3DS transaction for credit card to true
        Config::$is3ds = config('services.midtrans.is3ds');

        // Buat Array untuk dikirim ke midtrans
        $midtrans = [
            'transaction_details'=> [
                'order_id'=> $code,
                'gross_amount'=> (int) $request->total_price,
            ],
            'customer_details'=> [
                'first_name' => Auth::user()->name,
                'email' => Auth::user()->email,
            ],
            'enable_payment' => [
                'gopay','permata_va','bank_transfer'
            ],
            'vtweb' => []

            ];

            try {
            // Get Snap Payment Page URL
            $paymentUrl = Snap::createTransaction($midtrans)->redirect_url;
            
            // Redirect to Snap Payment Page
            return redirect($paymentUrl);
        }
        catch (Exception $e) {
            echo $e->getMessage();
        }

    }
    


    public function callback(Request $request)
    {
        // set konfigurasi midtrans
        Config::$serverKey = config('services.midtrans.serverKey');
        Config::$isProduction = config('services.midtrans.isProduction');
        Config::$isSanitized = config('services.midtrans.isSanitized');
        Config::$is3ds = config('services.midtrans.is3ds');

        //Instance midtrans notification
        $notification = new Notification();

        // Assign ke variable untuk memudahkan coding
        $status = $notification->transaction_status;
        $type = $notification->payment_type;
        $fraud = $notification->fraud_status;
        $order_id = $notification->order_id;
        
        //Cari transaksi berdasarkan ID
        $transaction = Transaction::findOrFail($order_id);

        // Handle notification status
        if($status == 'capture') {
            if($type == 'credit_card'){
                $transaction->status = 'PENDING';
            }
            else {
                $transaction->status = 'SUCCESS';
            }
        }

        else if($status == 'settlement') {
            $transaction->status = 'SUCCESS';
        }

        
        else if($status == 'pending') {
            $transaction->status = 'PENDING';
        }

        
        else if($status == 'deny') {
            $transaction->status = 'CANCELLED';
        }

        
        else if($status == 'expire') {
            $transaction->status = 'CANCELLED';
        }

        else if($status == 'cancel') {
            $transaction->status = 'CANCELLED';
        }
        // Simpan transaksi
        $transaction->save();

        
    }
}
