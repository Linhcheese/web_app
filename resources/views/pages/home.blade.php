@extends('layouts.master')
@section('styles')
<!-- Pikaday CSS -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/pikaday/1.8.2/css/pikaday.min.css">
<!-- Pikaday JS -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/pikaday/1.8.2/pikaday.min.js"></script>
<link rel="stylesheet" href="{{ asset('style/home.css') }}">
@endsection

@section('content')

@if(session('errors'))
<div id="toast-message" class="alert alert-success fixed top-5 right-5 bg-red-500 text-white px-4 py-2 rounded-md shadow-lg z-9999">
    {{ session('errors') }}
</div>
@endif
<div class="w-full fixed top-0 z-10">
<div class="home-header">
    <form method="GET" action="{{ route('home') }}" id="filter_date_home_page">
        <input type="text" id="monthPicker" style="border:none !important;background: none !important;color: white !important;font-size: 20px;text-align: center;font-weight: bold;" value="Tháng <?= $defaultMonthYear ?>" readonly>
        <input type="hidden" name="selected_date" id="selected_date" value="<?= $defaultMonthYear ?>">
    </form>
</div>
<div class="stastic">
    <div class="flex gap-3 w-full">
        <a class="active-subs">
            <div class="flex items-center gap-2">
                <img src="{{asset('svg/home/income.svg')}}" alt="">
                <span class="active-subs-title">Tổng thu nhâp</span>
            </div>
            <span class="receive">{{ number_format($data['income'] ?? 0)}} VNĐ</span>
        </a>
        <a class="active-subs">
            <div class="flex items-center gap-2">
                <img src="{{asset('svg/home/expense.svg')}}" alt="">
                <span class="active-subs-title">Tổng chi tiêu</span>
            </div>
            <span class="cost">{{ number_format($data['expense'] ?? 0)}} VNĐ</span>
        </a>
    </div>
    <div class="flex gap-3 w-full">
        <a href="{{ route('list-charity')}}" class="active-subs">
            <div class="flex items-center gap-2">
                <img src="{{asset('svg/home/charity.svg')}}" alt="">
                <span class="active-subs-title">Đóng góp của bạn</span>
            </div>
            <span class="receive text-[#EF5350]">{{ number_format($data['charity'] ?? 0)}} VNĐ</span>
        </a>
        <a href="{{route('list-goal')}}" class="active-subs">
            <div class="flex items-center gap-2">
                <img src="{{asset('svg/home/goal.svg')}}" alt="">
                <span class="active-subs-title">Mục tiêu của bạn</span>
            </div>
            <span class="cost text-[#FB8A00]">{{ number_format($data['goal'] ?? 0)}} VNĐ</span>
        </a>
    </div>
</div>
</div>
<!-- <p class="title-header text-center">Danh sách thu chi tháng này</p> -->
<div class="flex-row-b p-3 pt-[262px] mb-[30vh] tab-box">
    <div class="tabs">
        <div>
            <button class="tab-button {{$tabActive == 'statistic' ? 'active' : ''}}" onclick="openTab(event, 'statistic')">Thống kê</button>
            <button class="tab-button {{$tabActive == 'expense' ? 'active' : ''}}" onclick="openTab(event, 'expense')">Chi tiêu</button>
            <button class="tab-button {{$tabActive == 'income' ? 'active' : ''}}" onclick="openTab(event, 'income')">Thu nhập</button>
        </div>
    </div>

    <!--Expenses-->
    <div id="expense" class="tab-content {{$tabActive == 'expense' ? 'active' : ''}}">
        <div class="items">
            @forelse($dataExpenses as $item)

            <a href="{{ route('transaction.update-expense', ['id' => $item->id]) }}" class="items-sub ">
                <div class="flex items-center gap-2">
                    <div>
                        <img src="{{ asset('svg/home/expense.svg') }}" alt="">
                    </div>
                    <div class="grid gap-1">
                        <span class="text-white">{{ $item->name }}</span>
                        <span class="cost">{{ number_format($item->charge) }} VNĐ</span>
                        <span class="text-white">{{ $item->date }}</span>
                    </div>
                </div>
                <div class="flex items-center gap-2">
                    <form action="{{ route('transaction.delete-expense', $item->id) }}" method="POST" onsubmit="return confirm('Bạn có chắc chắn muốn xóa?');">
                        @csrf
                        <button type="submit" class="text-red-500" style="margin-top: 15px;">
                            <img src="{{ asset('svg/delete.svg') }}" alt="Xóa">
                        </button>
                    </form>
                    <img src="{{ asset('svg/arrow.svg') }}" alt="">
                </div>
            </a>
            @empty
            <p class="title-header text-center">Bạn hãy nhập chi tiêu nhé!</p>
            @endforelse
            <a style="color:white;text-align:center;text-decoration:underline;" href="{{ route('home-search', ['type' => 'expense'])}}">Xem nhiều hơn</a>
        </div>
    </div>

    <!--Income-->
    <div id="income" class="tab-content {{$tabActive == 'income' ? 'active' : ''}}">
        <div class="items">
            @forelse($dataIncomes as $item)
            <a href="{{ route('transaction.update-income', ['id' => $item->id]) }}" class="items-sub">
                <div class="flex items-center gap-2">
                    <div>
                        <img src="{{ asset('svg/home/income.svg') }}" alt="">
                    </div>
                    <div class="grid gap-1">
                        <span class="text-white">{{ $item->name }}</span>
                        <span class="receive">{{ number_format($item->charge) }} VNĐ</span>
                        <span class="text-white">{{ $item->date }}</span>
                    </div>
                </div>
                <div class="flex items-center gap-2">
                    <form  action="{{ route('transaction.delete-income', $item->id) }}" method="POST" onsubmit="return confirm('Bạn có chắc chắn muốn xóa?');">
                        @csrf
                        <button type="submit" class="text-red-500" style="margin-top: 15px;">
                            <img src="{{ asset('svg/delete.svg') }}" alt="Xóa">
                        </button>
                    </form>
                    <img src="{{ asset('svg/arrow.svg') }}" alt="">
                </div>
            </a>
            @empty
            <p class="title-header text-center">Bạn hãy nhập thu nhập nhé!</p>
            @endforelse
            <a style="color:white;text-align:center;text-decoration:underline;" href="{{ route('home-search', ['type' => 'income'])}}">Xem nhiều hơn</a>
        </div>
    </div>

    <div id="statistic" class="tab-content {{$tabActive == 'statistic' ? 'active' : ''}}">
        <p style="color: white;text-align: center;">
            <img style="width: 35px;height: 35px;display: inline;margin-right: 5px;" src="{{asset('svg/wallet.svg')}}" alt="">
            Số dư: {{ number_format((($data['income'] ?? 0) - ($data['expense'] ?? 0) - ($data['goal'] ?? 0) - ($data['charity'] ?? 0)) ?? 0)}} VNĐ
        </p>
        <div>
        @php
            $remain = (($data['income'] ?? 0) - ($data['expense'] ?? 0) - ($data['goal'] ?? 0) - ($data['charity'] ?? 0)) ?? 0;        @endphp
        @if ($remain <= 0)
            <p style="color: white;font-size: 15px; text-align: center;font-weight: bold;">
               <img style="display: inline;" src="{{ asset('svg/warning-icon.svg') }}" alt=""> Bạn đã tiêu hết tiền rồi.
               @if ($remain < 0)
                Bạn đang nợ {{ $remain }} VNĐ.
               @endif 
            </p>
        @elseif ( $remain <= ($data['income'] ?? 0) * 0.3)
            <p style="color: white;font-size: 15px;font-weight: bold;">
               <img style="display: inline;" src="{{ asset('svg/warning-icon.svg') }}" alt=""> Bạn đã tiêu gần hết tiền rồi. Hạn chế chi tiêu nhé.
            </p>
        @else
            <p style="color: white;font-size: 15px;font-weight: bold;">
               <img style="display: inline;" src="{{ asset('svg/warning-icon.svg') }}" alt="">Bạn chi tiêu thoải mái, nhưng nhớ hãy tiết kiệm!!!
            </p>
        @endif
        <canvas style="height: 300px; width: 300px ; margin-left: auto; margin-right: auto;margin-top: 15px;" id="myChart"></canvas>
    </div>
    </div>
</div>

@if (!$hasExpenseForToday)
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            let checkIsAlertReminder = localStorage.getItem("checkIsAlertReminder");
            let isHasExpenseToday = '<?php echo $hasExpenseForToday ?>';
            if (localStorage.getItem('alertDay') !== '<?php echo $currentDate ?>') {
                localStorage.removeItem('checkIsAlertReminder');
                localStorage.removeItem('alertDay');
                checkIsAlertReminder = 0;
                isHasExpenseToday = '';
            }
            if (checkIsAlertReminder != 1 && isHasExpenseToday == '') {
                document.getElementById('expenseReminderModal').classList.remove('hidden');
            }
        });
        function closeModal() {
            document.getElementById('expenseReminderModal').classList.add('hidden');
        }
        function noMoreAlert() {
            localStorage.setItem('checkIsAlertReminder', 1);
            localStorage.setItem('alertDay', '<?php echo $currentDate ?>');
            document.getElementById('expenseReminderModal').classList.add('hidden');
        }
    </script>
@endif

<!-- Modal -->
<div id="expenseReminderModal" class="fixed inset-0 flex items-center justify-center bg-gray-900 bg-opacity-50 hidden">
    <div class="bg-white p-6 rounded-lg shadow-lg w-96">
        <h2 class="text-lg font-bold text-gray-800">Nhắc nhở nhập chi tiêu</h2>
        <p class="mt-2 text-gray-600">Bạn chưa nhập chi tiêu cho ngày hôm nay. Hãy nhập ngay để quản lý tài chính tốt hơn!</p>

        <div class="mt-4 flex justify-end space-x-2">
            <button onclick="closeModal()" class="px-4 py-2 bg-gray-300 rounded-md hover:bg-gray-400">OK</button>
            <button onclick="noMoreAlert()" class="px-4 py-2 bg-gray-300 rounded-md hover:bg-gray-400">Hủy</button>
        </div>
    </div>
</div>

@endsection

<style>
    .tabs {
        display: flex;
        justify-content: center;
        align-items: center;
        position: fixed;
        width: 100%;
        z-index: 10;
        /* top: auto; */
        left: 0;
    }

    .tab-button {
        cursor: pointer;
        border: none;
        color: white;
        border-radius: 12px;
        padding: 8px;
        font-size: 14px;
        font-weight: 600;
        line-height: 20px;
    }

    .tab-button.active {
        background: #FBFFE4;
        color: #EC5228;
    }

    .tab-content {
        margin-top: 48px;
        display: none;
    }

    .tab-content.active {
        display: block;
        overflow: scroll;
        height: 85vh;
    }
    input[type=text]:focus {
        box-shadow: none;    
    }

    .tab-box {
        padding-top: 240px !important;
    }
</style>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    function openTab(event, tabId) {
        location.href = '/?tab_active=' + tabId;
    }
</script>
<script>
    document.addEventListener("DOMContentLoaded", function () {
        let picker = new Pikaday({
            field: document.getElementById('monthPicker'),
            format: 'dd/MM/YYYY',
            yearRange: [1900, 2100],
            onSelect: function (date) {
                let monthYear = ('0' + (date.getMonth() + 1)).slice(-2) + ' / ' + date.getFullYear();
                document.getElementById('monthPicker').value = "Tháng " + monthYear;
                document.getElementById('selected_date').value = monthYear;
                document.getElementById('filter_date_home_page').submit();
            }
        });

        // Set default display format
        document.getElementById('monthPicker').value = "Tháng " + document.getElementById('selected_date').value;
    });
    document.addEventListener("DOMContentLoaded", function () {
        // Lấy dữ liệu từ Blade
        const income = {{ $data['income'] ?? 0 }};
        const expense = parseInt({{ $data['expense'] ?? 0 }}) + parseInt({{ $data['goal'] ?? 0 }}) + parseInt({{ $data['charity'] ?? 0 }});
        const total = income + expense;

        const ctx = document.getElementById('myChart').getContext('2d');

        const myChart = new Chart(ctx, {
            type: 'pie', // Biểu đồ hình tròn
            scaleFontColor: "#EC5228",
            data: {
                labels: ['Thu nhập', 'Chi tiêu'],
                datasets: [{
                    data: [income, expense],
                    backgroundColor: ['#1ED760', '#D32F2F'], // Xanh lá cho thu nhập, đỏ cho chi tiêu
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            color: "#FFFFFF"
                        }
                    },
                    tooltip: {
                        callbacks: {
                            label: function (tooltipItem) {
                                let value = tooltipItem.raw;
                                let percentage = ((value / total) * 100).toFixed(2);
                                return `${tooltipItem.label}: ${percentage}%`;
                            }
                        }
                    }
                }
            }
        });
    });
</script>