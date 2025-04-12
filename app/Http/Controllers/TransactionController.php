<?php

namespace App\Http\Controllers;

use App\Http\Requests\GoalRequest;
use App\Http\Requests\MoneyExpenseRequest;
use App\Http\Requests\MoneyRequest;
use App\Models\CharityTransaction;
use App\Models\Expense;
use App\Models\Goal;
use App\Models\GoalTransaction;
use App\Models\Income;
use App\Models\Statistic;
use App\Services\CharityTransactionsService;
use App\Services\ExpenseService;
use App\Services\GoalService;
use App\Services\GoldTransactionsService;
use App\Services\IncomeService;
use App\Services\MExpenseService;
use App\Services\MIncomeService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TransactionController extends Controller
{
    private $expenseService;
    private $incomeService;
    private $charityTransactionsService;
    private $goldTransactionsService;
    private $goalService;
    private $mExpenseService;
    private $mIncomeService;

    public function __construct(
        ExpenseService $expenseService,
        IncomeService  $incomeService,
        CharityTransactionsService $charityTransactionsService,
        GoldTransactionsService $goldTransactionsService,
        GoalService $goalService,
        MExpenseService $mExpenseService,
        MIncomeService $mIncomeService
    ) {
        $this->expenseService = $expenseService;
        $this->incomeService = $incomeService;
        $this->charityTransactionsService = $charityTransactionsService;
        $this->goldTransactionsService = $goldTransactionsService;
        $this->goalService = $goalService;
        $this->mExpenseService = $mExpenseService;
        $this->mIncomeService = $mIncomeService;
    }

    //expense
    public function addExpenses()
    {
        $authUser = Auth::user();
        $userId = $authUser->id;

        $result = $this->mExpenseService->get([
            'user_id' => $userId
        ])->toArray();

        if (count($result) <= 0) {
            $listMIncome = config('default_master_data.m_expense');
            $insertData = [];
            foreach ($listMIncome as $item) {
                $insertData[] = [
                    'name' => $item,
                    'user_id' => $userId
                ];
            }
            if ($this->mExpenseService->insert($insertData)) {
                $result = $insertData;
            }
        }
        $month = Carbon::now()->format('m');
        $year = Carbon::now()->format('Y');
        $statistic = Statistic::where('user_id', $userId)->where('month', $month)->where('year', $year)->first();
        if ($statistic) {
            $statistic->remain = ($statistic->income * 0.7 - $statistic->expense) > 0 ? ($statistic->income * 0.7 - $statistic->expense) : 0;
        }
        return view('pages.add-expenses', compact('result', 'statistic'));
    }

    public function getUpdateExpense($id)
    {
        $authUser = Auth::user();
        $userId = $authUser->id;

        $result = $this->mExpenseService->get([
            'user_id' => $userId
        ])->toArray();

        if (count($result) <= 0) {
            $listMIncome = config('default_master_data.m_expense');
            $insertData = [];
            foreach ($listMIncome as $item) {
                $insertData[] = [
                    'name' => $item,
                    'user_id' => $userId
                ];
            }
            if ($this->mExpenseService->insert($insertData)) {
                $result = $insertData;
            }
        }

        $expense = Expense::find($id);

        return view('pages.expenses.update-expense', compact('result', 'expense'));
    }

    public function updateExpenses(MoneyExpenseRequest $request, $id)
    {
        $data = $request->only('charge', 'name', 'm_expense_id', 'date');
        $result = $this->expenseService->update([
            'id' => $id
        ], $data);

        return redirect()->route('home', ['tab_active' => 'expense'])->withSuccess('Cập nhật thành công');
    }

    public function deleteExpense($id)
    {
        $result = $this->expenseService->delete([
            'id' => $id
        ]);

        return redirect()->route('home', ['tab_active' => 'expense'])->withSuccess('Xóa thành công');
    }
    //income
    public function addIncome()
    {
        $authUser = Auth::user();
        $userId = $authUser->id;

        $result = $this->mIncomeService->get([
            'user_id' => $userId
        ])->toArray();

        if (count($result) <= 0) {
            $listMIncome = config('default_master_data.m_income');
            $insertData = [];
            foreach ($listMIncome as $item) {
                $insertData[] = [
                    'name' => $item,
                    'user_id' => $userId
                ];
            }
            if ($this->mIncomeService->insert($insertData)) {
                $result = $insertData;
            }
        }

        return view('pages.add-income', compact('result'));
    }

    public function getUpdateIncome($id)
    {
        $authUser = Auth::user();
        $userId = $authUser->id;

        $result = $this->mExpenseService->get([
            'user_id' => $userId
        ])->toArray();

        if (count($result) <= 0) {
            $listMIncome = config('default_master_data.m_expense');
            $insertData = [];
            foreach ($listMIncome as $item) {
                $insertData[] = [
                    'name' => $item,
                    'user_id' => $userId
                ];
            }
            if ($this->mExpenseService->insert($insertData)) {
                $result = $insertData;
            }
        }

        $income = Income::find($id);

        return view('pages.expenses.update-income', compact('result', 'income'));
    }

    public function updateIncome(Request $request, $id)
    {
        $data = $request->only('charge', 'name', 'm_expense_id', 'date');
        $result = $this->incomeService->update([
            'id' => $id
        ], $data);

        return redirect()->route('home', ['tab_active' => 'income'])->withSuccess('Cập nhật thành công');
    }

    public function incomeTransaction(MoneyRequest $request)
    {
        $data = $request->only('charge', 'name', 'm_income_id', 'date');
        $data['user_id'] = Auth::user()->id;
        if ($this->incomeService->insert($data)) {
            return redirect()->route('home', ['tab_active' => 'income'])->withSuccess('Thêm thành công');
        }

        return redirect()->route('home', ['tab_active' => 'income'])->withErrors(['Thêm thất bại !']);
    }

    public function expenseTransaction(MoneyExpenseRequest $request)
    {
        $data = $request->only('charge', 'name', 'm_expense_id', 'date');
        $data['user_id'] = Auth::user()->id;
        if ($this->expenseService->insert($data)) {
            return redirect()->route('home', ['tab_active' => 'expense'])->withSuccess('Thêm thành công');
        }

        return redirect()->route('home', ['tab_active' => 'expense'])->withErrors(['Thêm thất bại !']);
    }

    public function deleteIncome($id)
    {
        $result = $this->incomeService->delete([
            'id' => $id
        ]);

        return redirect()->route('home', ['tab_active' => 'income'])->withSuccess('Xóa thành công');
    }

    //goal
    public function addGoal()
    {
        return view('pages.goal.add-goal');
    }

    public function listGoal(Request $request)
    {
        $data = Goal::with('goalTransactions')->where('user_id', auth()->id());

        if (request()->has('date')) {
            $data->whereDate('due_date', request('date'));
        }

        if ($request->filled('status')) {
            if ($request->input('status') == '0') {
                $data->where('status', 0)->where('due_date', '>=', now()->toDateString());
            } elseif ($request->input('status') == '1') {
                $data->where('status', 1);
            } elseif ($request->input('status') == 'expired') {
                $data->where('status', 0)->whereDate('due_date', '<', now()->toDateString());
            }
        }

        $data = $data->get();

        $total =  Goal::with('goalTransactions')->where('user_id', auth()->id())->where('status', 1)->count();

        return view('pages.goal.list-goal', compact('data', 'total'));
    }

    public function addGoalTransaction($id)
    {
        $goal = Goal::with('goalTransactions')->where('id', $id)->first();
        return view('pages.goal.add-goal-transaction', compact('goal'));
    }

    public function goalTransaction(Request $request, $id)
    {
        $data = $request->only('charge');
        $data['user_id'] = Auth::user()->id;
        $data['m_saving_id'] = $id;
        if ($this->goldTransactionsService->insert($data)) {
            return redirect()->route('list-goal')->withSuccess('Thêm thành công');
        }

        return redirect()->route('list-goal')->withErrors(['Thêm thất bại !']);
    }

    public function detailGoal($id)
    {
        $data = GoalTransaction::with('goal')->where('m_saving_id', $id)->get();
        return view('pages.goal.detail-goal-transaction', compact('data'));
    }

    public function createGoal(GoalRequest $request)
    {
        $data = $request->only('charge', 'name', 'due_date');
        $data['user_id'] = Auth::user()->id;
        if ($this->goalService->insert($data)) {
            return redirect()->route('list-goal')->withSuccess('Thêm thành công');
        }

        return redirect()->route('list-goal')->withErrors(['Thêm thất bại !']);
    }

    public function deleteGoal($id)
    {
        $result = $this->goalService->delete(['id' => $id]);

        if (!$result) {
            return redirect()->route('list-goal')->withErrors('Xóa mục tiêu thất bại !');
        }

        return redirect()->route('list-goal')->withSuccess('Xóa thành công');
    }

    public function deleteGoalTransaction($id)
    {
        $result = $this->goldTransactionsService->delete(['id' => $id]);

        if (!$result) {
            return redirect()->route('list-goal')->withErrors('Xóa thất bại !');
        }

        return redirect()->route('list-goal')->withSuccess('Xóa thành công');
    }

    //charity
    public function addCharity()
    {
        return view('pages.charity.add-charity');
    }

    public function listCharity(Request $request)
    {
        $data = CharityTransaction::where('user_id', auth()->id());

        if (request()->has('date')) {
            $data->whereDate('created_at', request('date')); // Lọc theo field `date`
        }

        $data = $data->orderBy('created_at', 'desc')->get();
        $totalCharge =  CharityTransaction::where('user_id', auth()->id())->get()->sum('charge');
        return view('pages.charity.list-charity', compact('data', 'totalCharge'));
    }

    public function charityTransaction(Request $request)
    {
        $data = $request->only('charge', 'name');
        $data['user_id'] = Auth::user()->id;
        if ($this->charityTransactionsService->insert($data)) {
            return redirect()->route('list-charity')->withSuccess('Thêm thành công');
        }

        return redirect()->route('list-charity')->withErrors(['Thêm thất bại !']);
    }

    public function getUpdateCharity($id)
    {
        $charity = CharityTransaction::find($id);
        return view('pages.charity.update-charity', compact('charity'));
    }

    public function updateCharity(Request $request, $id)
    {
        $data = $request->only('charge', 'name');
        $result = $this->charityTransactionsService->update([
            'id' => $id
        ], $data);

        return redirect()->route('list-charity')->withSuccess('Cập nhật thành công');
    }

    public function deleteCharity($id)
    {
        $result = $this->charityTransactionsService->delete([
            'id' => $id
        ]);

        return redirect()->route('list-charity')->withSuccess('Xóa thành công');
    }
}
