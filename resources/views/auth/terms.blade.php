@extends('layouts.master')

@section('content')
<div class="page-wrapper" id="main-wrapper" data-layout="vertical" data-navbarbg="skin6" data-sidebartype="full" data-sidebar-position="fixed" data-header-position="fixed" style="transform: translateY(-71px);">
    <div class="position-relative overflow-hidden min-vh-100 d-flex align-items-center justify-content-center">
        <div class="d-flex align-items-center justify-content-center w-100">
            <div class="row justify-content-center w-100">
                <div class="px-1 py-3 col-sm-11">
                    <div class="card mb-0">
                        <div class="px-4 py-2 text-end">
                            <a href="{{ url()->previous() }}">
                                <button type="button" class="btn-close"></button>
                            </a>
                        </div>
                        <div class="card-body py-0 px-3">
                            <div class="mb-5">
                                <h3 class="text-center">이용약관</h3>
                            </div>                   
                            <div class="consent-form">
                                <div class="mb-4">
                                    <div class="form-check d-flex align-items-center mb-2">
                                        <input class="form-check-input" type="checkbox" id="agreeAll">
                                        <label class="form-check-label section-title fs-5 fw-semibold ms-2" for="agreeAll">
                                            전체 동의하기
                                        </label>
                                    </div>
                                    <div class="consent-text fs-4 text-gray">
                                        QORA 이용약관, 개인정보 수집 및 이용, 이벤트 및 혜택정보수신 동의를 포함합니다.
                                    </div>
                                </div>
                                
                                <!-- Terms Section 1 -->
                                <div class="mb-5">
                                    <div class="form-check d-flex justify-content-between align-items-center">
                                        <div class="d-flex align-items-center">
                                            <input class="form-check-input me-2" type="checkbox" id="terms1" required>
                                            <label class="form-check-label section-title fw-semibold fs-4" for="terms1">
                                                <span class="text-primary">[필수]</span> QORA 이용약관
                                            </label>
                                        </div>
                                        <a href="" class="btn btn-dark btn-sm">
                                            <span>전체보기</span>
                                        </a>
                                    </div>
                                    <div class="bg-light rounded-3 mt-3 scroll-container">
                                        <p class="m-0 p-4 text-gray">QORA 이용약관, 개인정보 수집 및 이용, 이벤트 및 혜택정보수신 동의를 포함합니다. QORA 이용약관, 개인정보 수집 및 이용, 이벤트 및 혜택정보수신 동의를 포함합니다.QORA 이용약관, 개인정보 수집 및 이용, 이벤트 및 혜택정보수신 동의를 포함합니다.</p>
                                    </div>                                    
                                </div>  
                                
                                <!-- Terms Section 2 -->
                                <div class="mb-5">
                                    <div class="form-check d-flex justify-content-between align-items-center">
                                        <div class="d-flex align-items-center">
                                            <input class="form-check-input me-2" type="checkbox" id="terms2" required>
                                            <label class="form-check-label section-title fw-semibold fs-4" for="terms2">
                                                <span class="text-primary">[필수]</span> 개인정보 수집 및 이용
                                            </label>
                                        </div>
                                        <a href="" class="btn btn-dark btn-sm">
                                            <span>전체보기</span>
                                        </a>
                                    </div>
                                    <div class="bg-light rounded-3 mt-3 scroll-container">
                                        <p class="m-0 p-4 text-gray">QORA 이용약관, 개인정보 수집 및 이용, 이벤트 및 혜택정보수신 동의를 포함합니다. QORA 이용약관, 개인정보 수집 및 이용, 이벤트 및 혜택정보수신 동의를 포함합니다.QORA 이용약관, 개인정보 수집 및 이용, 이벤트 및 혜택정보수신 동의를 포함합니다.</p>
                                    </div>                                    
                                </div>  
                                
                                <!-- Terms Section 3 -->
                                <div class="mb-5">
                                    <div class="form-check d-flex justify-content-between align-items-center">
                                        <div class="d-flex align-items-center">
                                            <input class="form-check-input me-2" type="checkbox" id="terms3" required>
                                            <label class="form-check-label section-title fw-semibold fs-4" for="terms3">
                                                <span class="text-primary">[필수]</span> 이벤트 및 혜택정보수신
                                            </label>
                                        </div>
                                        <a href="" class="btn btn-dark btn-sm">
                                            <span>전체보기</span>
                                        </a>
                                    </div>
                                    <div class="bg-light rounded-3 mt-3 scroll-container">
                                        <p class="m-0 p-4 text-gray">QORA 이용약관, 개인정보 수집 및 이용, 이벤트 및 혜택정보수신 동의를 포함합니다. QORA 이용약관, 개인정보 수집 및 이용, 이벤트 및 혜택정보수신 동의를 포함합니다.QORA 이용약관, 개인정보 수집 및 이용, 이벤트 및 혜택정보수신 동의를 포함합니다.</p>
                                    </div>                                    
                                </div>  
                            </div>
                            <div>
                                <span class="badge text-bg-info mt-4 mb-2">안내사항</span>
                                <p class="fs-6 fw-semibold mb-4">
                                    전자서명 시 하단 서명란에 "정자"로 이름을 확인할 수 있도록 작성해주세요.
                                </p>
                                
                                <div class="signature-container position-relative text-start mb-4">
                                    <div class="position-relative bg-light rounded w-100" style="margin: 0 auto; border: 1px solid #eee;">
                                        <button type="button" class="btn btn-sm btn-outline-primary mb-2 clear-btn position-absolute" style="top:16px; right: 16px;" onclick="clearSignature()">지우기</button>
                                        <canvas id="signatureCanvas" height="200" class="w-100">
                                            <!-- 브라우저가 Canvas를 지원하지 않습니다. -->
                                        </canvas>
                                        <div class="position-absolute top-0 start-0 w-100 h-100 d-flex align-items-center justify-content-center" id="placeholderText" style="pointer-events: none;">
                                            <span class="text-primary fs-7 fw-semibold opacity-50">이름 정자로 작성하세요.</span>
                                        </div>
                                    </div>
                                </div>
                                
                                <button type="submit" class="btn btn-primary w-100 py-3 fs-5 mt-4" onclick="submitForm()">
                                    다음
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


@endsection

@push('script')
<script>
    const canvas = document.getElementById('signatureCanvas');
    const ctx = canvas.getContext('2d');
    const placeholderText = document.getElementById('placeholderText');

    let isDrawing = false;
    let lastX = 0;
    let lastY = 0;
    let hasSignature = false;

    function resizeCanvas() {
        const rect = canvas.getBoundingClientRect();
        canvas.width = rect.width;
        canvas.height = rect.height;
        
        ctx.lineWidth = 3;
        ctx.lineCap = 'round';
        ctx.lineJoin = 'round';
        ctx.strokeStyle = '#777';
    }

    resizeCanvas();
    window.addEventListener('resize', resizeCanvas);

    function getCoordinates(e) {
        const rect = canvas.getBoundingClientRect();
        let x, y;
        
        if (e.touches && e.touches.length > 0) {
            // 터치 이벤트
            x = e.touches[0].clientX - rect.left;
            y = e.touches[0].clientY - rect.top;
        } else {
            // 마우스 이벤트
            x = e.clientX - rect.left;
            y = e.clientY - rect.top;
        }
        
        return { x, y };
    }

    function draw(e) {
        if (!isDrawing) return;
        
        const coords = getCoordinates(e);
        
        ctx.beginPath();
        ctx.moveTo(lastX, lastY);
        ctx.lineTo(coords.x, coords.y);
        ctx.stroke();
        
        lastX = coords.x;
        lastY = coords.y;
    }

    function startDrawing(e) {
        isDrawing = true;
        const coords = getCoordinates(e);
        lastX = coords.x;
        lastY = coords.y;
        
        if (!hasSignature) {
            const placeholder = document.getElementById('placeholderText');
            placeholder.style.display = 'none';
            placeholder.style.visibility = 'hidden';
            placeholder.style.opacity = '0';
            hasSignature = true;
            console.log('플레이스홀더 숨김 완료');
        }
        
        ctx.beginPath();
        ctx.arc(coords.x, coords.y, 1, 0, Math.PI * 2);
        ctx.fillStyle = '#777';
        ctx.fill();
        
        e.preventDefault();
    }

    function stopDrawing(e) {
        if (!isDrawing) return;
        isDrawing = false;
        ctx.beginPath();
        e.preventDefault();
    }

    // 마우스 이벤트 (데스크톱)
    canvas.addEventListener('mousedown', startDrawing);
    canvas.addEventListener('mousemove', draw);
    canvas.addEventListener('mouseup', stopDrawing);
    canvas.addEventListener('mouseout', stopDrawing);

    // 터치 이벤트 (모바일)
    canvas.addEventListener('touchstart', startDrawing, { passive: false });
    canvas.addEventListener('touchmove', draw, { passive: false });
    canvas.addEventListener('touchend', stopDrawing, { passive: false });
    canvas.addEventListener('touchcancel', stopDrawing, { passive: false });

    function clearSignature() {
        ctx.clearRect(0, 0, canvas.width, canvas.height);
        const placeholder = document.getElementById('placeholderText');
        placeholder.style.display = 'flex';
        placeholder.style.visibility = 'visible';
        placeholder.style.opacity = '1';
        hasSignature = false;
    }

    document.getElementById('agreeAll').addEventListener('change', function() {
        const checkboxes = document.querySelectorAll('#terms1, #terms2, #terms3');
        checkboxes.forEach(checkbox => {
            checkbox.checked = this.checked;
        });
    });

    document.querySelectorAll('#terms1, #terms2, #terms3').forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            const allChecked = document.querySelectorAll('#terms1:checked, #terms2:checked, #terms3:checked').length === 3;
            document.getElementById('agreeAll').checked = allChecked;
        });
    });

    // 폼 제출 함수
    function submitForm() {
        const terms1 = document.getElementById('terms1').checked;
        const terms2 = document.getElementById('terms2').checked;
        const terms3 = document.getElementById('terms3').checked;
        
        if (!terms1 || !terms2 || !terms3) {
            alert('모든 필수 약관에 동의해주세요.');
            return false;
        }
        
        if (!hasSignature) {
            alert('서명을 작성해주세요.');
            return false;
        }
        
        alert('약관 동의 및 서명이 완료되었습니다.');
        // 실제 폼 제출 로직 추가
    }
</script>
@endpush