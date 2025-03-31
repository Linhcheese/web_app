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
        <a href="{{route('home')}}" class="icons-back">
             <img src="{{asset('svg/arrow-back.svg')}}" alt="">
        </a>
        <span class="add-expenses-title">Khuyến nghị chi tiêu</span>
        <span></span>
    </div>
    <form method="POST" action="{{route('idea_plan')}}" class="p-3" id="spendingForm">
        <input type="hidden" name="charge" value="{{$charge}}">
        @csrf
        <div id="questions-container">
            @foreach ($questions as $index => $question)
                <div class="question mt-4 p-4 border rounded-lg {{ $index !== 0 ? 'hidden' : '' }}">
                    <p class="text-lg font-semibold" style="color: white;">{{ $question }}</p>
                    @foreach ($options[$index] as $key => $option)
                        <label class="block mt-2">
                            <input type="radio" name="answers[{{ $index }}]" value="{{ chr(97 + $key) }}" class="mr-2" required <?php echo $key == 0 ? 'checked' : ''; ?>> {{ $option }}
                        </label>
                    @endforeach
                </div>
            @endforeach
        </div>

        <div class="mt-6 flex justify-between">
            <button type="button" id="prevBtn" class="px-4 py-2 bg-gray-400 text-white rounded hidden" onclick="prevQuestion()">Quay lại</button>
            <button type="button" id="nextBtn" class="button-add-expenses" style="width: 100px;" onclick="nextQuestion()">Tiếp theo</button>
            <button type="submit" id="submitBtn" class="button-add-expenses" style="width: 200px;">Hoàn thành</button>
        </div>
    </form>
</div>
<script>
    let currentQuestion = 0;
    const questions = document.querySelectorAll('.question');
    const prevBtn = document.getElementById('prevBtn');
    const nextBtn = document.getElementById('nextBtn');
    const submitBtn = document.getElementById('submitBtn');

    function showQuestion(index) {
        questions.forEach((q, i) => q.classList.toggle('hidden', i !== index));
        prevBtn.classList.toggle('hidden', index === 0);
        nextBtn.classList.toggle('hidden', index === questions.length - 1);
        submitBtn.classList.toggle('hidden', index !== questions.length - 1);
    }

    function nextQuestion() {
        if (currentQuestion < questions.length - 1) {
            currentQuestion++;
            showQuestion(currentQuestion);
        }
    }

    function prevQuestion() {
        if (currentQuestion > 0) {
            currentQuestion--;
            showQuestion(currentQuestion);
        }
    }

    window.onload = function() {
        showQuestion(0);
    };
</script>
@endsection