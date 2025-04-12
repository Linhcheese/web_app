@extends('layouts.master')

@section('styles')
<link rel="stylesheet" href="{{asset('style/add-expenses.css')}}">
@endsection

@section('content')
<div class="add-expenses-header">
    <a href="{{route('list-goal')}}" class="icons-back">
        <img src="{{asset('svg/arrow-back.svg')}}" alt="">
    </a>
    <span class="add-expenses-title">Thêm giao dịch mục tiêu</span>
    <span></span>
</div>
<form action="{{route('transaction.goal',['id'=>$goal['id']])}}" method="post" class="p-3">
    <span class="text-white" style="color: white !important;">Tên: {{$goal['name'] }}</span>
    <br>
    <span class="text-white" style="color: white !important;">Thời hạn: {{$goal['due_date'] }}</span>
    <br>
    <span class="dollar text-white" style="color: white !important;">
        Bạn đã hoàn thành: {{ number_format($goal->goalTransactions->sum('charge')) }} / {{ number_format($goal['charge']) }}
    </span>
    @csrf
    <div class="add-expenses">
        <div class="add-expenses-sub">
                <label for="charge">Số tiền bạn tiết kiệm</label>
                <input type="text" id="charge" class="input-expenses" placeholder="Nhập số tiền" required>
                <input type="hidden" name="charge" id="charge-hidden">
        </div>
    </div>

    <!-- Submit Button -->
    <div class="add-expenses-sub text-center mt-4">
        <button type="submit" class="button-add-expenses">Tiết kiệm</button>
    </div>
</form>
@endsection

<script>
document.addEventListener('DOMContentLoaded', function () {
    const chargeInput = document.getElementById('charge');
    const chargeHidden = document.getElementById('charge-hidden');

    chargeInput.addEventListener('input', function (e) {
        let rawValue = this.value.replace(/\D/g, ''); // Loại bỏ tất cả ký tự không phải số
        let formattedValue = new Intl.NumberFormat('en-US').format(rawValue); // Định dạng kiểu 1.000.000

        this.value = formattedValue; // Hiển thị số có dấu chấm
        chargeHidden.value = rawValue; // Lưu giá trị không có dấu chấm vào input ẩn
    });
});
</script>