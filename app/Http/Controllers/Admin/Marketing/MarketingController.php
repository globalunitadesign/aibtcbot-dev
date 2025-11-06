<?php

namespace App\Http\Controllers\Admin\Marketing;

use App\Exports\StakingPolicyExport;
use App\Models\LanguagePolicy;
use App\Models\Marketing;
use App\Models\MarketingTranslation;
use App\Http\Controllers\Controller;
use App\Models\ReferralPolicy;
use App\Models\ReferralMatchingPolicy;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;

class MarketingController extends Controller
{

    public function list(Request $request)
    {
        $marketings = Marketing::all();

        return view('admin.marketing.list', compact('marketings'));
    }

    public function view(Request $request)
    {
        $locale = LanguagePolicy::where('type', 'locale')->first()->content;

        $view = Marketing::find($request->id);
        $translations = MarketingTranslation::where('marketing_id', $view->id)->get();

        return view('admin.marketing.view', compact('locale','view', 'translations'));
    }

    public function create(Request $request)
    {
        $locale = LanguagePolicy::where('type', 'locale')->first()->content;

        return view('admin.marketing.create', compact('locale'));
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
                'file' => 'required|image|mimes:jpeg,png,jpg,svg|max:5120',
            ]);

            $file_name = time() . '_marketing_' . $file->getClientOriginalName();

            $file_path = $file->storeAs('uploads/marketing', $file_name, 'public');
            $file_url[] = asset('storage/uploads/marketing/' . $file_name);

            $data['is_required'] = $request->is_required;
            $data['image_urls'] = $file_url;
            $data['benefit_rules'] = $request->benefit_rules;

            DB::beginTransaction();

            try {

                $marketing = Marketing::create($data);

                $locales = $request->translation;

                foreach ($locales as $code => $locale) {
                    MarketingTranslation::create([
                        'marketing_id' => $marketing->id,
                        'locale' => $code,
                        'name' => $locale['name'],
                        'memo' => $locale['memo'],
                    ]);
                }

                for ($i=1; $i < 14; $i++) {
                    ReferralPolicy::create([
                       'marketing_id' => $marketing->id,
                       'grade_id' => $i,
                    ]);

                    ReferralMatchingPolicy::create([
                        'marketing_id' => $marketing->id,
                        'grade_id' => $i,
                    ]);
                }

                DB::commit();

                return response()->json([
                    'status' => 'success',
                    'message' => '마케팅 상품이 추가되었습니다.',
                    'url' => route('admin.marketing.list'),
                ]);

            } catch (\Exception $e) {
                DB::rollBack();

                Log::error('Failed to create marketing', ['error' => $e->getMessage()]);

                return response()->json([
                    'status' => 'error',
                    'message' => '예기치 못한 오류가 발생했습니다.',
                ]);
            }
        }

        return response()->json([
            'status' => 'error',
            'message' => '예기치 못한 오류가 발생했습니다.',
        ]);
    }

    public function update(Request $request)
    {
        $marketing = Marketing::find($request->id);

        $data['is_required'] = $request->is_required;
        $data['image_urls'] = $marketing->image_urls;
        $data['benefit_rules'] = $request->benefit_rules;

        if ($request->hasFile('file')) {

            $file = $request->file('file');

            if ($file->isValid()) {

                $validated = $request->validate([
                    'file' => 'required|image|mimes:jpeg,png,jpg,svg|max:5120',
                ]);

                $file_name = time() . '_marketing_' . $file->getClientOriginalName();

                $file_path = $file->storeAs('uploads/marketing', $file_name, 'public');
                $file_url[] = asset('storage/uploads/marketing/' . $file_name);

                $data['image_urls'] = $file_url;

                $old_file_path = $marketing->image_urls[0];

                if (Storage::disk('public')->exists($old_file_path)) {
                    Storage::disk('public')->delete($old_file_path);
                }

            }
        }

        DB::beginTransaction();

        try {

            $marketing->update($data);

            $locales = $request->translation;

            foreach ($locales as $locale) {


                $marketing_translation = MarketingTranslation::find($locale['id']);

                $marketing_translation->update([
                    'name' => $locale['name'],
                    'memo' => $locale['memo'],
                ]);
            }

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => '마케팅 상품이 수정되었습니다.',
                'url' => route('admin.marketing.list'),
            ]);

        } catch (\Exception $e) {

            Log::error('Failed to update marketing', ['error' => $e->getMessage()]);

            return response()->json([
                'status' => 'error',
                'message' => '예기치 못한 오류가 발생했습니다.',
            ]);
        }
    }

    public function export()
    {
        $current = now()->toDateString();

        return Excel::download(new StakingPolicyExport(), '스테이킹 상품 내역 ' . $current . '.xlsx');
    }
}
