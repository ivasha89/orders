<?php

namespace App\Http\Controllers;

use App\Order;
use App\Tarif;
use App\Client;
use Illuminate\Http\Request;

class SpaController extends Controller
{
    public function index()
    {
        return view('spa');
    }

    public function tarifs(){
        $tarifs = Tarif::all()->toArray();
        return $tarifs;
    }

    public function orders() {
        $orders = Order::all();
        foreach ($orders as $key => $order) {
            $array[$key] = [
                'address' => $order->address,
                'date' => $order->deliver_date,
                'name' => $order->client->name . " " . $order->client->last_name,
                'phone' => $order->client->phone,
            ];
        }

        return $array;
    }

    public function makeOrder(Request $request) {
        $clientExist = false;
        try {
            $client = Client::where('phone', $request->phone)->first();
            if ($client) {
                $clientExist = true;
            }
            else {
                $client = Client::create([
                    'name' => $request->name,
                    'last_name' => $request->lastName,
                    'phone' => $request->phone,
                ]);
            }

            $order = new Order([
                'address' => $request->address,
                'deliver_date' => date('Y-m-d', strtotime($request->date)),
            ]);
            $client->orders()->save($order);

            $message = [
                'success' => $clientExist ? 'Вы уже делали заказ. Будет сделан еще один' : 'Заказ оформлен'
            ];
        }
        catch (\Throwable $exception) {
            $message = [
                'error' => 'Ошибка'];
        }

        return $message;
    }
}
