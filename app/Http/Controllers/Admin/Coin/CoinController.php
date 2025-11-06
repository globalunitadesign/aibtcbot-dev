<?php

namespace App\Http\Controllers\Admin\Coin;


use App\Models\Coin;
use App\Models\User;
use App\Models\Asset;
use App\Models\Income;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CoinController extends Controller
{
    public function index(Request $request)
    {

        $list = Coin::paginate(10);


        return view('admin.coin.coin', compact('list'));

    }

    public function store(Request $request)
    {
        if (!$request->hasFile('file')) {

            return response()->json([
                'status' => 'error',
                'message' => '이미지를 첨부해주세요.',
            ]);

        }

        $file = $request->file('file');

        if ($file->isValid()) {

            $validated = $request->validate([
                'code' => 'required|string|max:10',
                'name' => 'required|string|max:50',
                'address' => 'string|max:255',
                'file' => 'required|image|mimes:jpeg,png,jpg,svg|max:5120',
            ]);

            $data = [];

            $data['code'] = $validated['code'];
            $data['name'] = $validated['name'];
            $data['address'] = $validated['address'];

            $file_name = '_' . time() . '_' . auth()->id() . '_' . $file->getClientOriginalName();

            $file_path = $file->storeAs('uploads/coin', $file_name, 'public');
            $file_url[] = asset('storage/uploads/coin/' . $file_name);

            $data['image_urls'] = $file_url;


            DB::beginTransaction();

            try {

                $coin = Coin::create($data);

                $users = User::all();

                foreach ($users as $user) {
                    Asset::create([
                        'user_id' => $user->id,
                        'coin_id' => $coin->id,
                        'balance' => 0,
                    ]);

                    Income::create([
                        'user_id' => $user->id,
                        'coin_id' => $coin->id,
                        'balance' => 0,
                    ]);
                }

                DB::commit();

            } catch (\Exception $e) {

                DB::rollBack();

                \Log::error('Failed to insert coin', ['error' => $e->getMessage()]);

                return response()->json([
                    'status' => 'error',
                    'message' => '예기치 못한 오류가 발생했습니다.',
                ]);
            }

        } else {
            return response()->json([
                'status' => 'error',
                'message' => '잘못된 이미지입니다.',
            ]);

        }

        return response()->json([
            'status' => 'success',
            'message' => '코인이 추가되었습니다.',
            'url' => route('admin.coin'),
        ]);
    }

    public function update(Request $request)
    {
        DB::beginTransaction();

        try {

            Coin::where('id', $request->id)->update([
                'address' => $request->address,
                'is_active' => $request->is_active,
                'is_asset' => $request->is_asset,
                'is_income' => $request->is_income,
                'is_mining' => $request->is_mining,
            ]);

            DB::commit();

        } catch (\Exception $e) {

            DB::rollBack();

            Log::error('Failed to update coin', ['error' => $e->getMessage()]);

            return response()->json([
                'status' => 'error',
                'message' => '예기치 못한 오류가 발생했습니다.',
            ]);
        }

        return response()->json([
            'status' => 'success',
            'message' => '코인이 수정되었습니다.',
            'url' => route('admin.coin'),
        ]);
    }

}
