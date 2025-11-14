@extends('admin.layouts.master')

@section('content')
<div class="body-wrapper">
    <div class="container-fluid">
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="card-title">{{ __('수동 입금') }}</h5>
                </div>
                <form method="POST" action="{{ route('admin.asset.deposit.store') }}" id="ajaxForm">
                    @csrf
                    <hr>
                    <table class="table table-bordered mt-5 mb-5">
                        <tbody>
                            <tr>
                                <th class="text-center align-middle">아이디</th>
                                <td class="align-middle">{{ $user->account }}</td>
                                <th class="text-center align-middle">이름</th>
                                <td class="align-middle">{{ $user->name }}</td>
                            </tr>
                            <tr>
                                <th class="text-center align-middle">종류</th>
                                <td class="align-middle">
                                    <select name="id" class="form-select w-50">
                                        @foreach($user->member->assets as $asset)
                                        <option value="{{ $asset->id }}">{{ $asset->coin->name }}</option>
                                        @endforeach
                                    </select>
                                </td>
                                <th class="text-center align-middle">수량</th>
                                <td class="align-middle">
                                    <input type="text" name="amount" class="form-control w-50">
                                </td>
                            </tr>
                        </tbody>
                    </table>
                    <hr>
                    <div class="d-flex justify-content-end align-items-center">
                        <div>
                            <button type="submit" class="btn btn-danger">입금</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
