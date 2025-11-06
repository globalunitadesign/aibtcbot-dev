@extends('admin.layouts.master')

@section('content')
<div class="body-wrapper">
    <div class="container-fluid">
        <div class="card">
            <div class="card-body">
                <div class="mb-3 d-flex justify-content-between">
                    <h5 class="card-title">관리자 추가</h5>
                </div>
                <form method="POST" action="{{ route('admin.manager.store') }}" id="ajaxForm">
                    @csrf    
                    <hr>
                    <table class="table table-bordered mt-5 mb-5">
                        <tbody>
                            <tr>
                                <th class="text-center align-middle">아이디</th>
                                <td class="align-middle">
                                    <input type="text" name="account" class="form-control w-50 required" required>
                                </td>
                                <th class="text-center align-middle">비밀번호</th>
                                <td class="align-middle">
                                    <input type="password" name="password" id="inputPassword" class="form-control w-50 required" required>
                                </td>
                            </tr>
                            <tr>
                                <th class="text-center align-middle">이름</th>
                                <td class="align-middle">
                                    <input type="text" name="name" class="form-control w-50 required" required>
                                </td>
                                
                                <th class="text-center align-middle">레벨</th>
                                <td class="align-middle">
                                    <select name="admin_level" class="form-control w-25 required" required>
                                        <option value="">레벨 선택</option>
                                        <option value="1">1</option>
                                        <option value="2">2</option>
                                        <option value="3">3</option>
                                    </select>
                                </td>
                            </tr>
                            <tr>
                                <td colspan="4">
                                    <h5 class="text-center mt-5">관리자 등급별 권한</h5>
                                </td>
                            </tr>
                            <tr>
                                <td colspan="4" class="">
                                    <table class="table table-bordered mb-2">
                                        <tr>
                                            <th class="text-center algin-middle">1레벨</th>
                                            <td class="algin-middle">
                                                회원조회, 조직도, KYC 인증 심사, 게시글 관리(공지사항, 1대1문의, 상품소개, 가이드북), 언어설정(언어)
                                            </td>
                                        </tr>
                                        <tr>
                                            <th class="text-center algin-middle">2레벨</th>
                                            <td class="algin-middle">
                                                자산 관리, 수익 관리, 마이닝, 게시글 관리(이용약관, 이벤트, 회사소개)
                                            </td>
                                        </tr>
                                        <tr>
                                            <th class="text-center algin-middle">3레벨</th>
                                            <td class="algin-middle">
                                                회원 등급 관리, 코인 관리, 자산 수동입금, 게시판 관리, 정책 관리, 언어 설정(기본, 메시지) 
                                            </td>
                                        </tr>
                                    </table>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                    <hr>
                    <div class="d-flex justify-content-between align-items-center">
                        <a href="{{ route('admin.manager.list') }}" class="btn btn-secondary">목록</a>
                        <button type="submit" class="btn btn-danger">추가</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection

@push('script')
<script src="{{ asset('js/admin/manager/create.js') }}"></script>
@endpush