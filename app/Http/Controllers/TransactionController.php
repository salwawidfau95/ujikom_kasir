<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Detail;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\Member;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Illuminate\Support\Str;
use App\Exports\TransactionsExport;
use Maatwebsite\Excel\Facades\Excel;

class TransactionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $user = Auth::user();

        $search = $request->input('search');
        $entries = $request->input('entries', 10); // default 10

        $transactions = Transaction::with(['user', 'member', 'detail.product'])
            ->when($search, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('total_price', 'like', "%$search%")
                        ->orWhere('created_at', 'like', "%$search%")
                        ->orWhereHas('user', function ($userQuery) use ($search) {
                            $userQuery->where('username', 'like', "%$search%");
                        })
                        ->orWhereHas('member', function ($memberQuery) use ($search) {
                            $memberQuery->where('name', 'like', "%$search%");
                        })
                        ->orWhereHas('detail.product', function ($detailQuery) use ($search) {
                            $detailQuery->where('name', 'like', "%$search%");
                        });
                });
            })
            ->paginate($entries)
            ->appends($request->query());

        return view('transactions.admin.index', compact('transactions', 'search', 'entries', 'user'));
    }

    public function index2(Request $request)
    {
        $user = Auth::user();


        $search = $request->input('search');
        $entries = $request->input('entries', 10); // default 10


        // Ambil filter tanggal
        $year = $request->input('year');
        $month = $request->input('month');
        $date = $request->input('date');


        $transactions = Transaction::with(['user', 'member', 'detail.product'])
            ->when($search, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('total_price', 'like', "%$search%")
                        ->orWhere('created_at', 'like', "%$search%")
                        ->orWhereHas('user', function ($userQuery) use ($search) {
                            $userQuery->where('username', 'like', "%$search%");
                        })
                        ->orWhereHas('member', function ($memberQuery) use ($search) {
                            $memberQuery->where('name', 'like', "%$search%");
                        })
                        ->orWhereHas('detail.product', function ($detailQuery) use ($search) {
                            $detailQuery->where('name', 'like', "%$search%");
                        });
                });
            })
            ->when($year, function ($query, $year) {
                $query->whereYear('created_at', $year);
            })
            ->when($month, function ($query, $month) {
                $query->whereMonth('created_at', $month);
            })
            ->when($date, function ($query, $date) {
                $query->whereDate('created_at', $date);
            })
            ->paginate($entries)
            ->appends($request->query());


        return view('transactions.staff.index', compact('transactions', 'search', 'entries', 'user', 'year', 'month', 'date'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $products = Product::all();
        return view('transactions.staff.create', compact('products'));
    }

    public function confirm(Request $request)
    {
        $quantities = $request->quantities ?? [];

        $selectedProducts = [];
        $total = 0;

        foreach ($quantities as $productId => $qty) {
            if ($qty > 0) {
                $product = Product::findOrFail($productId);
                $price = $product->price;
                $subtotal = $price * $qty;

                $selectedProducts[] = [
                    'product_id' => $product->id,
                    'name' => $product->name,
                    'qty' => $qty,
                    'price' => $price,
                    'subtotal' => $subtotal,
                ];

                $total += $subtotal;
            }
        }

        Session::put('cart', $selectedProducts);
        Session::put('total', $total);

        return view('transactions.staff.confirm', compact('selectedProducts', 'total'));
    }

    public function finalize(Request $request)
    {
        $request->validate([
            'total_payment' => 'required|numeric|min:0|max:9999999999',
            'member_status' => 'required|in:member,non-member',
        ]);

        $cart = Session::get('cart', []);
        $totalPrice = Session::get('total', 0);
        $change = $request->total_payment - $totalPrice;
        $user = Auth::user();

        $today = now()->format('Ymd');
        $random = strtoupper(Str::random(5));
        $transactionCode = "TRX-{$today}-{$random}";

        $memberId = null;
        $member = null;
        $points = 0;

        if ($request->member_status === 'member') {
            $member = Member::where('no_phone', $request->no_phone)->first();

            if (!$member) {
                // Buat member baru
                $member = Member::create([
                    'no_phone' => $request->no_phone,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                // Member baru, poin awal 10% dari total belanja
                $points = intval($totalPrice * 0.01);
            } else {
                // Member lama, tambah 10% dari total belanja ke poin sekarang
                $points = $member->point + intval($totalPrice * 0.01);
            }

            $memberId = $member->id;

            // Update poin ke database
            $member->update([
                'point' => $points,
            ]);
        }

        $transaction = Transaction::create([
            'transaction_code' => $transactionCode,
            'user_id' => $user->id,
            'member_id' => $memberId,
            'total_price' => $totalPrice,
            'total_payment' => $request->total_payment,
            'change' => $change,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $transactionItems = [];

        foreach ($cart as $item) {
            $transactionItem = Detail::create([
                'transaction_id' => $transaction->id,
                'product_id' => $item['product_id'],
                'quantity' => $item['qty'],
                'price' => $item['price'],
                'subtotal' => $item['subtotal'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            $transactionItems[] = $transactionItem;

            Product::where('id', $item['product_id'])->decrement('stock', $item['qty']);
        }

        Session::forget(['cart', 'total']);

        if ($request->member_status === 'member') {
            return view('transactions.staff.member', [
                'transaction' => $transaction,
                'items' => $transactionItems,
                'total_price' => $totalPrice,
                'total_payment' => $request->total_payment,
                'change' => $change,
                'user' => $user,
                'member' => $member,
                'points' => $points,
            ]);
        } else {
            return view('transactions.staff.receipt', [
                'transaction' => $transaction,
                'items' => $transactionItems,
                'total_price' => $totalPrice,
                'total_payment' => $request->total_payment,
                'change' => $change,
                'user' => $user,
                'member_points' => null,
                'date' => now()->format('Y-m-d H:i:s'),
                'used_point' => 0,
            ]);
        }
    }


    public function receiption($id, Request $request)
    {
        $transaction = Transaction::with('detail', 'member')->findOrFail($id);
        $items = $transaction->detail;
        $member = $transaction->member;
        $usePoints = (int) $request->input('use_points', 0);
        $totalPrice = (int) $request->input('total_price', 0);
        $totalPayment = (int) $request->input('total_payment', 0);
        $change = $totalPayment - $totalPrice;
        $usedPoint = 0;

        // ✅ Update nama member jika ada input baru
        $newName = $request->input('name');
        if ($member && $newName && $newName !== $member->name) {
            $member->update([
                'name' => $newName
            ]);
        }

        // ✅ Proses penggunaan poin
        if ($member && $usePoints && $member->point > 0) {
            $usablePoints = min($member->point, $totalPrice);
            $totalPrice -= $usablePoints;
            $usedPoint = $usablePoints;

            $change = $totalPayment - $totalPrice;

            // Update poin member
            $member->update([
                'point' => $member->point - $usablePoints,
            ]);

            // Update total price dan change di transaksi
            $transaction->update([
                'total_price' => $totalPrice,
                'change' => $change,
            ]);
        }

        return view('transactions.staff.receipt', [
            'transaction' => $transaction,
            'items' => $items,
            'total_price' => $totalPrice,
            'total_payment' => $totalPayment,
            'change' => $change,
            'user' => Auth::user(),
            'member_points' => $member?->point,
            'used_point' => $usedPoint,
            'date' => now()->format('Y-m-d H:i:s'),
        ]);
    }                    

    public function downloadReceipt($id)
    {
        $transaction = Transaction::with([
            'user:id,username',
            'member:id,no_phone,created_at,point',
            'detail.product:id,name'
        ])->findOrFail($id);

        $member = $transaction->member;
        $isMember = $member !== null;

        $data = [
            'store_name'        => 'TamiMarket',
            'member_status'     => $isMember ? 'Member' : 'Non Member',
            'no_phone'          => $member->no_phone ?? '-',
            'joined_since'      => $member && $member->created_at
                                    ? $member->created_at->format('d-m-Y')
                                    : '-',
            'point_member'      => $member->point ?? '-',
            'items'             => $transaction->detail,
            'total_price'       => $transaction->total_price,
            'change'            => $transaction->change,
            'price_after_point' => $member && $member->point > 0
                                    ? $transaction->total_price - $member->point
                                    : 0,
            'created_at'        => $transaction->created_at,
            'cashier'           => $transaction->user->username,
        ];

        $pdf = PDF::loadView('transactions.admin.download', $data);
        return $pdf->download('receipt.pdf');
    }

    public function downloadReceipt2($id)
    {
        $transaction = Transaction::with([
            'user:id,username',
            'member:id,no_phone,created_at,point',
            'detail.product:id,name'
        ])->findOrFail($id);

        $member = $transaction->member;
        $isMember = $member !== null;

        $data = [
            'store_name'        => 'TamiMarket',
            'member_status'     => $isMember ? 'Member' : 'Non Member',
            'no_phone'          => $member->no_phone ?? '-',
            'joined_since'      => $member && $member->created_at
                                    ? $member->created_at->format('d-m-Y')
                                    : '-',
            'point_member'      => $member->point ?? '-',
            'items'             => $transaction->detail,
            'total_price'       => $transaction->total_price,
            'change'            => $transaction->change,
            'price_after_point' => $member && $member->point > 0
                                    ? $transaction->total_price - $member->point
                                    : 0,
            'created_at'        => $transaction->created_at,
            'cashier'           => $transaction->user->username,
        ];

        $pdf = PDF::loadView('transactions.staff.download', $data);
        return $pdf->download('receipt.pdf');
    }

    public function show($id)
    {
        $transaction = Transaction::with(['detail.product', 'user', 'member'])->findOrFail($id);
        return response()->json([
            'transaction' => $transaction,
        ]);
    }

    public function export(Request $request)
    {
        return Excel::download(new TransactionsExport($request), 'report-purchases.xlsx');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Transaction $transaction)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Transaction $transaction)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Transaction $transaction)
    {
        //
    }
}
