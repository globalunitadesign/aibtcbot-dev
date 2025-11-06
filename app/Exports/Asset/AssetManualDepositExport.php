<?php

namespace App\Exports\Asset;

use App\Exports\BaseAssetExport;
use Illuminate\Support\Facades\DB;

class AssetManualDepositExport extends BaseAssetExport
{
    public function collection()
    {
        $query = DB::table('asset_transfers')
            ->leftJoin('assets', 'asset_transfers.asset_id', '=', 'assets.id')
            ->leftJoin('coins', 'assets.coin_id', '=', 'coins.id')
            ->leftJoin('users', 'asset_transfers.user_id', '=', 'users.id')
            ->select(
                'users.id',
                'users.name',
                'coins.name as coin_name',
                'asset_transfers.amount',
                'asset_transfers.actual_amount',
                'asset_transfers.status',
                'asset_transfers.fee',
                'asset_transfers.tax',
                'asset_transfers.created_at'
            )
            ->orderBy('asset_transfers.created_at', 'asc');

        $statusMap = $this->getStatusMap();

        $results = $this->applyCommonFilters($query)->get();

        return $this->formatExportRows($results);
    }


    public function headings(): array
    {
        return ['번호', 'UID', '이름', '종류', '신청수량', '실제수량', '상태', '수수료', '세금', '일자'];
    }
}
