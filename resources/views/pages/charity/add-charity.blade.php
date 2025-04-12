@extends('layouts.master')

@section('styles')
    <link rel="stylesheet" href="{{asset('style/add-expenses.css')}}">
@endsection

@section('content')
    @if(session('success'))
        <div id="toast-message" class="alert alert-success fixed top-5 right-5 bg-green-500 text-white px-4 py-2 rounded-md shadow-lg">
            {{ session('success') }}
        </div>
    @endif
    <div class="add-expenses-header">
        <a href="{{route('list-charity')}}" class="icons-back">
             <img src="{{asset('svg/arrow-back.svg')}}" alt="">
        </a>
        <span class="add-expenses-title">Ủng hộ quỹ cộng đồng</span>
        <span></span>
    </div>
    <form action="{{route('transaction.charity')}}" method="post" class="p-3">
        @csrf
        <div class="add-expenses">

            <!--Input-->
            <div class="add-expenses-sub">
                <label for="charge">Tiền đóng góp của bạn</label>
                <input type="text" id="charge" class="input-expenses" placeholder="Nhập số tiền ủng hộ" required>
                <input type="hidden" name="charge" id="charge-hidden">
            </div>

            <!--Description-->
            <div class="add-expenses-sub">
                <label for="charge">Mô tả</label>
                <textarea style="background: #1D1D1D; border-radius: 12px; border: 0.8px solid #979797; width: 100%;" 
                class="text-white" name="name" placeholder="Mô tả" required></textarea>
            </div>
        </div>

        <!-- Submit Button -->
        <div class="add-expenses-sub text-center mt-4">
            <button type="submit" class="button-add-expenses">Thêm</button>
        </div>
    </form>
@endsection
<script>
document.addEventListener('DOMContentLoaded', function () {
    const chargeInput = document.getElementById('charge');
    const chargeHidden = document.getElementById('charge-hidden');

    chargeInput.addEventListener('input', function () {
        let rawValue = this.value.replace(/\D/g, ''); // Xóa tất cả ký tự không phải số
        let formattedValue = new Intl.NumberFormat('en-US').format(rawValue); // Format thành 1,000,000

        this.value = formattedValue; // Hiển thị số đã format
        chargeHidden.value = rawValue; // Lưu giá trị gốc không có dấu phân tách
    });
});
</script>
