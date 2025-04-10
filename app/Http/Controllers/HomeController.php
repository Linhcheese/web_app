<?php

namespace App\Http\Controllers;

use App\Http\Requests\MoneyPlanRequest;
use App\Models\CharityTransaction;
use App\Models\Expense;
use App\Models\Goal;
use App\Models\GoalTransaction;
use App\Models\Income;
use App\Models\MExpense;
use App\Models\MIncome;
use App\Models\Statistic;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
    public function index(Request $request)
    {
        $currentDate = Carbon::now()->format('Y-m-d');
        $userId = auth()->user()->id;
        $selectedDate = $request->get('selected_date');
        $month = Carbon::now()->format('m');
        $year = Carbon::now()->format('Y');
        $defaultMonthYear = Carbon::now()->format('m / Y');
        if (!empty($selectedDate)) {
            $splitDate = explode('/', $selectedDate);
            $month = sprintf("%2d", (int) $splitDate[0]);
            $year = (int) $splitDate[1];
            $defaultMonthYear = $selectedDate;
        }
        $data = Statistic::where('user_id', $userId)->where('month', $month)->where('year', $year)->first();
        $dataExpenses = Expense::where('user_id', $userId)->whereBetween('date', [Carbon::now()->firstOfMonth(), Carbon::now()->lastOfMonth()])->orderBy('created_at', 'desc')->limit(4)->get();
        $dataIncomes = Income::where('user_id', $userId)->whereBetween('date', [Carbon::now()->firstOfMonth(), Carbon::now()->lastOfMonth()])->orderBy('created_at', 'desc')->limit(4)->get();
        $now = Carbon::now(); // Lấy thời gian hiện tại
        $today = Carbon::today();
        $user = Auth::user();

        $hasExpenseForToday = $user->expenses()->whereDate('created_at', $today)->exists();

        $tabActive = $request->get('tab_active') ? $request->get('tab_active') : 'statistic';
        $extendClass = 'disable-scroll';
        return view('pages.home', compact('currentDate', 'extendClass', 'tabActive','dataExpenses', 'dataIncomes', 'data', 'hasExpenseForToday', 'defaultMonthYear'));
    }

    public function idea()
    {
        $titlePage = 'Khuyến nghị chi tiêu';
        return view('pages.idea', compact('titlePage'));
    }

    public function moneyPlan(MoneyPlanRequest $request)
    {
        $questions = [
            "Khi nhận lương, bạn thường làm gì đầu tiên?",
            "Bạn có theo dõi thu nhập và chi tiêu của mình không?",
            "Bạn dành bao nhiêu % thu nhập cho tiết kiệm và đầu tư?",
            "Bạn có thường xuyên mua sắm những món đồ không cần thiết không?",
            "Khi cần mua một món đồ đắt tiền, bạn sẽ làm gì?",
            "Bạn có quỹ dự phòng cho các trường hợp khẩn cấp không?",
            "Bạn có thói quen lập ngân sách hàng tháng không?",
            "Bạn có chi tiêu quá tay vào các dịp lễ, sinh nhật hay ngày đặc biệt không?",
            "Bạn có hay bị hết tiền trước ngày nhận lương không?",
            "Khi có thu nhập đột xuất (thưởng, quà tặng), bạn sẽ làm gì?",
            "Bạn có sử dụng thẻ tín dụng không?",
            "Bạn thường chi bao nhiêu tiền cho ăn uống bên ngoài mỗi tháng?",
            "Bạn có kế hoạch tài chính dài hạn không?",
            "Khi cần tiền gấp, bạn thường làm gì?",
            "Bạn có thói quen đầu tư không?",
            "Bạn thường đặt mục tiêu tài chính như thế nào?",
            "Bạn có hay so sánh giá trước khi mua sắm không?",
            "Bạn có thói quen ghi chép chi tiêu hàng ngày không?",
            "Bạn có nghĩ rằng mình đang kiểm soát tài chính tốt không?",
            "Bạn có thường xuyên đặt mục tiêu tiết kiệm không?",
            "Khi bạn thấy một chương trình giảm giá hấp dẫn, bạn sẽ làm gì?",
            "Bạn có bao nhiêu nguồn thu nhập khác ngoài lương chính?",
            "Khi đi du lịch, bạn chi tiêu thế nào?",
            "Khi bạn có khoản nợ, bạn xử lý thế nào?",
            "Bạn có hay tham gia đầu tư tài chính không?",
            "Khi có khoản thu nhập tăng thêm, bạn sẽ làm gì?",
            "Bạn có quỹ tiết kiệm hưu trí không?",
            "Bạn có thói quen kiểm tra tài khoản ngân hàng bao lâu một lần?",
            "Khi cần một món đồ, bạn sẽ làm gì?",
            "Bạn có từng đặt ra mục tiêu tài chính trong năm không?",
            "Bạn có thường xuyên chi tiêu vào các khoản không cần thiết không?",
            "Bạn cảm thấy thế nào về việc dùng thẻ tín dụng?",
            "Bạn có thường xuyên ăn uống tại nhà hay ngoài hàng?",
            "Bạn có dành tiền cho việc học tập hoặc phát triển bản thân không?",
            "Bạn cảm thấy thế nào khi thấy số dư tài khoản của mình giảm xuống thấp?",
            "Bạn có lập kế hoạch mua sắm trước hay không?",
            "Bạn có thường xuyên sử dụng các ứng dụng tài chính để theo dõi chi tiêu không?",
            "Bạn có thường xuyên kiểm tra các khoản phí ngân hàng và thẻ tín dụng của mình không?",
            "Khi có khoản tiền lớn, bạn sẽ làm gì?",
            "Bạn có từng lập kế hoạch tài chính cho tương lai xa chưa?",
            "Khi bạn muốn đi du lịch, bạn sẽ làm gì?",
            "Khi bạn thấy một sản phẩm mới ra mắt nhưng chưa cần gấp, bạn sẽ làm gì?",
            "Bạn có hay sử dụng dịch vụ trả góp không?",
            "Bạn có đặt giới hạn cho số tiền tối đa có thể tiêu trong một tháng không?",
            "Khi bạn bè rủ đi ăn uống hoặc mua sắm bất ngờ, bạn sẽ làm gì?",
            "Bạn thường lên kế hoạch tài chính cá nhân trong bao lâu?",
            "Khi bạn thấy số dư tài khoản thấp hơn mong đợi, bạn sẽ làm gì?",
            "Bạn có theo dõi các khoản nợ (nếu có) của mình không?",
            "Bạn có chi tiêu theo cảm xúc không?",
            "Khi được tăng lương, bạn sẽ làm gì?",
            "Bạn có dành tiền cho việc học tập hoặc phát triển bản thân không?",
            "Khi có một khoản chi tiêu lớn bất ngờ, bạn sẽ làm gì?",
            "Bạn có bao giờ đầu tư tiền không?",
            "Bạn có thường xuyên sử dụng các ứng dụng tài chính để theo dõi chi tiêu không?",
            "Khi thấy một món đồ đang giảm giá mạnh, bạn sẽ làm gì?",
            "Khi có một khoản nợ, bạn sẽ ưu tiên gì?",
            "Bạn có hay mượn tiền bạn bè hoặc gia đình khi thiếu tiền không?",
            "Khi đi siêu thị, bạn có lập danh sách mua sắm trước không?",
            "Bạn có từng đặt mục tiêu tiết kiệm cho một mục đích cụ thể không?",
            "Khi bạn gặp vấn đề tài chính, bạn sẽ làm gì?",
        ];
        
        $options = [
            ["Mua sắm hoặc ăn mừng.", "Chuyển một phần vào tiết kiệm/đầu tư.", "Dành một phần cho chi tiêu, phần còn lại tiết kiệm.", "Chi tiêu tùy hứng, không có kế hoạch."],
            ["Không, tôi chi tiêu theo cảm tính.", "Có, tôi ghi chép chi tiết.", "Tôi theo dõi sơ bộ nhưng không quá nghiêm túc.", "Tôi không quan tâm đến việc này."],
            ["Ít hơn 10% hoặc không có kế hoạch.", "Từ 30% trở lên.", "Khoảng 10-20%.", "Không có quỹ tiết kiệm, tiêu hết những gì có."],
            ["Có, tôi thích mua những gì mình thích.", "Không, tôi chỉ mua khi thật sự cần.", "Thỉnh thoảng, nếu thấy hợp lý.", "Tôi mua sắm không kiểm soát, có khi mua rồi hối hận."],
            ["Mua ngay nếu thích.", "Tiết kiệm trước rồi mới mua.", "Cân nhắc ngân sách và kế hoạch tài chính.", "Mua bằng thẻ tín dụng hoặc vay mượn."],
            ["Không, tôi dùng thẻ tín dụng khi cần.", "Có, tôi dành ít nhất 6 tháng chi phí sinh hoạt.", "Tôi có một ít nhưng không quá nhiều.", "Tôi thường phải vay mượn khi gặp vấn đề tài chính."],
            ["Không, tôi thích chi tiêu linh hoạt.", "Có, tôi có bảng kế hoạch chi tiêu rõ ràng.", "Tôi lập ngân sách nhưng đôi khi không tuân thủ.", "Tôi không quan tâm đến việc này."],
            ["Có, tôi thường mua nhiều quà tặng, tổ chức tiệc.", "Không, tôi giữ mức chi tiêu hợp lý.", "Tôi chi tiêu có kế hoạch nhưng vẫn tận hưởng.", "Tôi chi tiêu tùy hứng, không kiểm soát."],
            ["Không bao giờ, tôi quản lý tài chính tốt.", "Hiếm khi, vì tôi có quỹ dự phòng.", "Đôi khi, nhưng tôi có thể xoay sở.", "Thường xuyên, tôi luôn phải vay mượn."],
            ["Dùng ngay để mua sắm hoặc du lịch.", "Tiết kiệm hoặc đầu tư toàn bộ.", "Dành một phần để tiết kiệm, phần còn lại để tận hưởng.", "Chi tiêu hết ngay mà không suy nghĩ."],
            ["Có, tôi dùng để chi tiêu thoải mái.", "Có, nhưng luôn trả hết đúng hạn.", "Tôi chỉ dùng trong trường hợp cần thiết.", "Tôi dùng nhiều và đôi khi quên thanh toán."],
            ["Hơn 50% thu nhập của tôi.", "Dưới 10% thu nhập.", "Khoảng 20-30%, nhưng vẫn có kế hoạch.", "Tôi không kiểm soát, ăn uống theo sở thích."],
            ["Không, tôi chỉ nghĩ đến hiện tại.", "Có, tôi đã có kế hoạch đầu tư và tiết kiệm.", "Tôi có ý tưởng nhưng chưa thực sự triển khai.", "Tôi không nghĩ đến việc này."],
            ["Rút tiền từ thẻ tín dụng hoặc vay mượn.", "Dùng quỹ dự phòng.", "Cân nhắc giảm bớt chi tiêu.", "Tôi không có sẵn tiền mặt, phải nhờ bạn bè."],
            ["Không, tôi thích giữ tiền mặt để tiêu.", "Có, tôi đầu tư vào cổ phiếu, quỹ, bất động sản, v.v.", "Tôi có nhưng chưa thực sự chuyên sâu.", "Tôi không biết gì về đầu tư."],
            ["Tôi không có mục tiêu tài chính cụ thể.", "Tôi có mục tiêu rõ ràng và kế hoạch thực hiện.", "Tôi có mục tiêu nhưng linh hoạt theo tình hình.", "Tôi không biết phải đặt mục tiêu như thế nào."],
            ["Không, tôi mua ngay khi thích.", "Có, tôi luôn tìm giá tốt nhất.", "Tôi cân nhắc nhưng không quá khắt khe.", "Tôi mua theo cảm hứng, không quan tâm giá cả."],
            ["Không, tôi thấy việc này phiền phức.", "Có, tôi theo dõi rất chi tiết.", "Tôi ghi chép nhưng không quá thường xuyên.", "Tôi không quan tâm đến việc này."],
            ["Không, tôi chỉ sống theo sở thích.", "Có, tôi luôn theo dõi tài chính chặt chẽ.", "Tôi nghĩ là ổn nhưng đôi khi vẫn bị thiếu hụt.", "Không, tôi thường xuyên gặp vấn đề tiền bạc."],
            ["Không, tôi cứ tiêu hết rồi tính sau.", "Có, tôi luôn có mục tiêu cụ thể.", "Tôi có nhưng đôi khi không tuân thủ.", "Tôi chưa bao giờ đặt mục tiêu tiết kiệm."],
            ["Mua ngay để không bỏ lỡ cơ hội.", "Kiểm tra xem mình có thực sự cần không trước khi mua.", "Xem xét ngân sách rồi quyết định.", "Thường xuyên mua dù không cần."],
            ["Không có, tôi chỉ sống bằng lương.", "Có nhiều nguồn thu nhập thụ động.", "Tôi có thêm 1-2 nguồn thu nhập phụ.", "Tôi chưa bao giờ nghĩ đến việc có thu nhập phụ."],
            ["Không giới hạn, miễn là vui.", "Có kế hoạch tài chính trước.", "Dành một khoản cố định, nhưng vẫn linh hoạt.", "Hay chi tiêu quá tay, hết tiền trước khi về."],
            ["Trả khi nào có tiền, không ưu tiên.", "Trả dứt điểm càng sớm càng tốt.", "Trả góp theo kế hoạch đã lập.", "Tôi thường xuyên vay mượn mà không lo trả nợ."],
            ["Không, tôi thích giữ tiền mặt.", "Có, tôi luôn tìm cơ hội đầu tư.", "Tôi có đầu tư nhưng khá thận trọng.", "Tôi không biết đầu tư là gì."],
            ["Tiêu hết để tận hưởng cuộc sống.", "Đầu tư hoặc tiết kiệm toàn bộ.", "Dành một phần để tiết kiệm, phần còn lại để chi tiêu.", "Không có kế hoạch, tiêu theo cảm hứng."],
            ["Không, tôi chưa nghĩ xa vậy.", "Có, tôi đã lên kế hoạch dài hạn.", "Tôi có nhưng chưa đều đặn.", "Tôi không quan tâm đến việc này."],
            ["Rất ít, chỉ khi cần rút tiền.", "Hàng ngày hoặc hàng tuần.", "Khi cảm thấy cần thiết.", "Tôi không nhớ lần cuối kiểm tra tài khoản là khi nào."],
            ["Mua ngay lập tức.", "So sánh giá cả trước khi quyết định.", "Đợi đến đợt giảm giá hoặc săn khuyến mãi.", "Vay tiền để mua nếu không đủ tiền."],
            ["Không, tôi thích sống thoải mái.", "Có, tôi luôn có mục tiêu cụ thể.", "Tôi có nhưng không thực sự nghiêm túc.", "Tôi chưa bao giờ nghĩ đến việc này."],
            ["Có, tôi thích mua những gì mình thích.", "Không, tôi luôn kiểm soát chi tiêu.", "Tôi có nhưng chỉ trong giới hạn nhất định.", "Tôi không kiểm soát được thói quen chi tiêu của mình."],
            ["Là công cụ hữu ích để chi tiêu thoải mái.", "Chỉ nên dùng khi cần thiết và luôn trả đúng hạn.", "Tôi có dùng nhưng không phụ thuộc.", "Tôi thường xuyên bị nợ thẻ tín dụng."],
            ["Tôi thích ăn ngoài, không nấu ăn.", "Tôi chủ yếu tự nấu ăn để tiết kiệm.", "Tôi có ăn ngoài nhưng vẫn nấu ăn tại nhà.", "Tôi không quan tâm đến chi tiêu ăn uống."],
            ["Không, tôi không muốn tốn tiền vào việc này.", "Có, tôi luôn đầu tư vào kiến thức và kỹ năng.", "Tôi có nhưng không thường xuyên.", "Tôi chưa bao giờ nghĩ đến việc này."],
            ["Không quan tâm, miễn là vẫn còn tiền tiêu.", "Lo lắng và tìm cách điều chỉnh chi tiêu.", "Cảm thấy không thoải mái nhưng vẫn chấp nhận được.", "Không quan tâm và tiếp tục tiêu tiền."],
            ["Không, tôi mua bất cứ khi nào thích.", "Có, tôi luôn lên danh sách trước khi mua.", "Tôi lập kế hoạch nhưng đôi khi mua thêm.", "Tôi không có thói quen này."],
            ["Không, tôi không thấy cần thiết.", "Có, tôi sử dụng chúng hàng ngày.", "Tôi có nhưng không dùng thường xuyên.", "Tôi không quan tâm đến việc này."],
            ["Không, tôi không quan tâm.", "Có, tôi luôn theo dõi để tránh lãng phí.", "Tôi kiểm tra khi có nghi ngờ.", "Tôi không biết cách kiểm tra."],
            ["Tiêu ngay để tận hưởng.", "Đầu tư hoặc tiết kiệm dài hạn.", "Chia thành nhiều khoản khác nhau.", "Tôi không có kế hoạch rõ ràng."],
            ["Chưa bao giờ, tôi chỉ nghĩ đến hiện tại.", "Có, tôi luôn có kế hoạch rõ ràng.", "Tôi có nhưng chưa thực sự chi tiết.", "Tôi không nghĩ đến việc này."],
            ["Đặt vé ngay mà không cần tính toán.", "Tiết kiệm trước rồi mới đi.", "Cân nhắc ngân sách và điều chỉnh kế hoạch nếu cần.", "Đợi có tiền dư mới nghĩ đến việc đi."],
            ["Mua ngay để trải nghiệm.", "Chờ đánh giá và cân nhắc có thực sự cần không.", "Xem xét tài chính trước khi quyết định.", "Không quan tâm vì không có kế hoạch mua sắm."],
            ["Rất thường xuyên, vì giúp mua sắm dễ dàng hơn.", "Hạn chế, chỉ dùng khi thực sự cần thiết.", "Thỉnh thoảng dùng nếu có lợi ích tài chính.", "Không thích trả góp vì sợ nợ nần."],
            ["Không, tiêu tùy hứng.", "Có, và luôn tuân thủ.", "Có nhưng linh hoạt điều chỉnh nếu cần.", "Có nhưng hiếm khi tuân theo."],
            ["Luôn đồng ý, không nghĩ về tài chính.", "Xem xét ngân sách trước khi quyết định.", "Cố gắng cân bằng giữa tài chính và các mối quan hệ.", "Từ chối nếu không có sẵn tiền."],
            ["Không có kế hoạch cụ thể.", "Lập kế hoạch chi tiết theo tháng/năm.", "Có kế hoạch sơ bộ, điều chỉnh theo thực tế.", "Chỉ nghĩ đến khi gặp vấn đề tài chính."],
            ["Không quan tâm, cứ tiếp tục chi tiêu.", "Điều chỉnh ngay và tìm cách tiết kiệm.", "Xem lại chi tiêu để điều chỉnh dần dần.", "Cảm thấy lo lắng nhưng không biết làm gì."],
            ["Không, miễn là có thể trả nợ hàng tháng.", "Có, và luôn có kế hoạch trả hết sớm.", "Theo dõi nhưng không quá khắt khe.", "Chỉ nhớ đến khi bị nhắc nhở."],
            ["Rất thường xuyên.", "Rất hiếm, luôn có kế hoạch trước.", "Đôi khi nếu thấy hợp lý.", "Không để ý, cứ thấy thích là mua."],
            ["Tăng chi tiêu ngay.", "Tăng khoản tiết kiệm/đầu tư.", "Cân đối giữa chi tiêu và tiết kiệm.", "Không có kế hoạch cụ thể."],
            ["Không, vì không thấy cần thiết.", "Luôn dành một phần thu nhập cho việc này.", "Dành tiền nếu thấy có ích.", "Không nghĩ đến nhưng có thể sẽ làm sau."],
            ["Dùng ngay tiền có sẵn mà không suy nghĩ nhiều.", "Tìm cách điều chỉnh ngân sách để bù đắp.", "Vay mượn hoặc trả góp nếu cần thiết.", "Hoãn lại nếu không đủ tiền."],
            ["Không, vì thấy rủi ro.", "Có, và tìm hiểu kỹ trước khi đầu tư.", "Đầu tư nhưng chưa có chiến lược rõ ràng.", "Muốn đầu tư nhưng chưa biết bắt đầu từ đâu."],
            ["Không cần thiết.", "Có, và theo dõi rất sát sao.", "Đôi khi dùng nhưng không đều đặn.", "Có nhưng không thực sự tận dụng hết tính năng."],
            ["Mua ngay vì sợ hết khuyến mãi.", "Xem xét có thực sự cần không rồi mới mua.", "So sánh giá và tìm hiểu thêm trước khi quyết định.", "Thường bị dụ mua nhưng sau đó hối hận."],
            ["Trả dần theo lịch trả góp.", "Cố gắng trả hết nhanh nhất có thể.", "Cân đối với các khoản chi khác để không ảnh hưởng quá nhiều.", "Chỉ trả khi bị nhắc nhở."],
            ["Thường xuyên.", "Rất hiếm, vì luôn có kế hoạch tài chính.", "Chỉ khi thực sự cần thiết.", "Đôi khi nhưng không thích làm vậy."],
            ["Không, cứ thích gì mua nấy.", "Luôn có danh sách và tuân thủ.", "Có danh sách nhưng vẫn linh hoạt.", "Lập danh sách nhưng hiếm khi làm theo."],
            ["Không, cứ có tiền là tiêu.", "Có, và kiên trì theo đuổi.", "Đặt mục tiêu nhưng đôi khi không đạt được.", "Thích nhưng chưa có kế hoạch cụ thể."],
            ["Không quan tâm, chờ tự giải quyết.", "Lập kế hoạch xử lý ngay.", "Tìm kiếm lời khuyên và điều chỉnh dần.", "Vay mượn để giải quyết trước."]
        ];
        
        $selectedQuestions = $this->getRandomQuestions($questions, $options, 5);
        return view('pages.money_plan_question', [
            'charge' => $request->get('charge'),
            'questions' => $selectedQuestions['questions'],
            'options' => $selectedQuestions['options']
        ]);
    }

    private function getRandomQuestions($questions, $options, $num = 10) {
        $randomKeys = array_rand($questions, $num);
        $selectedQuestions = [];
        $selectedOptions = [];
        foreach ($randomKeys as $key) {
            $selectedQuestions[] = $questions[$key];
            $selectedOptions[] = $options[$key];
        }

        $selectedQuestions[] = "Kế hoạch chi tiêu sắp tới của bạn là gì?";
        $selectedOptions[] = ["Cân bằng giữa tận hưởng cuộc sống và tiết kiệm", "Tận hưởng cuộc sống", "Tiết kiệm cho tương lai", "Chưa có kế hoạch cụ thể"];

        return [
            'questions' => $selectedQuestions,
            'options' => $selectedOptions
        ];
    }

    private function calculateMoneyPlanType($request)
    {
        $answers = $request->input('answers', []);
        $scoreA = $scoreB = $scoreC = $scoreD = 0;

        foreach ($answers as $answer) {
            if ($answer == 'a') {
                $scoreA++;
            } elseif ($answer == 'b') {
                $scoreB++;
            } elseif ($answer == 'c') {
                $scoreC++;
            } elseif ($answer == 'd') {
                $scoreD++;
            }
        }

        if ($scoreC > $scoreA && $scoreC > $scoreB && $scoreC > $scoreD) {
            return 1;
        } elseif ($scoreA > $scoreC && $scoreA > $scoreB && $scoreA > $scoreD) {
            return 2;
        } elseif ($scoreB > $scoreC && $scoreB > $scoreA && $scoreB > $scoreD) {
            return 3;
        } else {
            return 4;
        }
    }

    public function ideaPlan(Request $request)
    {
        $titlePage = 'Khuyến nghị chi tiêu';
        //Tao plan chi tieu
        $planData = [];
        $charge = $request->get('charge');
        $answers = $request->input('answers', []);
        $futurePlan = $answers[array_key_last($answers)];
        $type = 3;
        if ($futurePlan == 'b') {
            $type = 1;
        } elseif ($futurePlan == 'c') {
            $type = 2;
        }

        //Generate current plan evaluate note
        $previousMoneyPlan = $this->calculateMoneyPlanType($request);
        switch ($previousMoneyPlan) {
            case 1:
                $evaluateNote = [
                    'Bạn coi trọng trải nghiệm và không quá lo lắng về việc tiết kiệm.',
                    'Bạn sẵn sàng chi tiền cho những thứ mang lại niềm vui tức thì như du lịch, ăn uống, mua sắm.',
                    'Tuy nhiên, nếu không có kế hoạch tài chính dài hạn, bạn có thể gặp khó khăn trong tương lai khi có những tình huống bất ngờ xảy ra.'
                ];
                break;
            case 2:
                $evaluateNote = [
                    'Bạn có tư duy tài chính tốt, luôn tính toán cẩn thận trước khi chi tiêu.',
                    'Bạn ưu tiên tiết kiệm, đầu tư và kiểm soát chi tiêu để tối đa hóa lợi ích tài chính.',
                    'Có thể bạn sẽ ít khi tận hưởng cuộc sống hoặc cảm thấy áp lực khi phải tiết kiệm quá nhiều.'
                ];
                break;
            case 3:
                $evaluateNote = [
                    'Bạn vừa có kế hoạch tài chính, vừa cho phép bản thân tận hưởng cuộc sống.',
                    'Bạn biết cách phân bổ thu nhập hợp lý: một phần để tiết kiệm/đầu tư, một phần để chi tiêu cá nhân.',
                    'Đây là cách tiếp cận tài chính bền vững, giúp bạn duy trì cuộc sống thoải mái mà vẫn có sự an toàn tài chính.'
                ];
                break;
            case 4:
                $evaluateNote = [
                    'Bạn không có kế hoạch tài chính rõ ràng và chi tiêu dựa trên cảm xúc.',
                    'Có thể bạn không biết mình đã tiêu bao nhiêu tiền mỗi tháng hoặc không có quỹ tiết kiệm dự phòng.',
                    'Điều này có thể khiến bạn dễ rơi vào khó khăn tài chính khi gặp các tình huống bất ngờ.'
                ];
                break;
        }


        
        //Calculate future plan note
        switch ($type) {
            // Hưởng thụ thoải mái 
            case 1:
                $typeNote[] = 'Có vẻ bạn vẫn tận hưởng tuổi trẻ của mình.';
                $typeNote[] = 'Đừng quên tiết kiệm một chút nhé!';
                $typeName = 'Tận hưởng cuộc sống';
                $planData = [
                    'nhu_cau_co_ban' => $charge * 0.5,
                    'giai_tri' => $charge * 0.3,
                    'tiet_kiem' => $charge * 0.1,
                    'khac' => $charge * 0.1,
                    'note'  => 'Người muốn tận hưởng cuộc sống, không quá áp lực về tài chính, thu nhập đủ cao để đảm bảo nhu cầu.',
                    'advantage' => [
                        "Cuộc sống thoải mái, không bị gò bó tài chính.",
                        "Nhiều trải nghiệm thú vị, tận hưởng cuộc sống hiện tại.",
                        "Tạo động lực kiếm tiền vì có thể chi tiêu theo sở thích.",
                    ],
                    'disadvantage' => [
                        "Tiết kiệm ít, có thể gặp khó khăn khi có tình huống khẩn cấp.",
                        "Khả năng đạt được mục tiêu tài chính dài hạn thấp.",
                        "Dễ rơi vào vòng xoáy chi tiêu quá mức nếu không kiểm soát tốt.",
                    ]
                ];
                break;
            // Tiết kiệm tối đa
            case 2:
                $typeNote[] = 'Duy trì thói quen tốt này nhé';
                $typeNote[] = 'Giấc mơ của bạn sẽ không còn xa nữa đâu!';
                $typeName = 'Tiết kiệm cho tương lai';
                $planData = [
                    'nhu_cau_co_ban' => $charge * 0.5,
                    'giai_tri' => $charge * 0.4,
                    'tiet_kiem' => $charge * 0.05,
                    'khac' => $charge * 0.05,
                    'note'  => 'Người muốn tối ưu tài chính, nhanh chóng đạt được mục tiêu tiết kiệm lớn hoặc độc lập tài chính sớm.',
                    'advantage' => [
                        "Tích lũy tài chính nhanh, đảm bảo tương lai ổn định.",
                        "Có thể đầu tư sinh lời sớm, đạt tự do tài chính nhanh hơn.",
                        "Luôn có quỹ dự phòng, ít lo lắng về tài chính trong khủng hoảng.",
                    ],
                    'disadvantage' => [
                        "Cuộc sống có thể bị gò bó, ít trải nghiệm giải trí, du lịch.",
                        "Dễ cảm thấy bị thắt lưng buộc bụng và thiếu động lực sống.",
                        "Nếu tiết kiệm quá mức, có thể bỏ lỡ nhiều cơ hội tận hưởng cuộc sống."
                    ]
                ];
                break;
            //Cân bằng
            default:
                $typeNote[] = 'Cân bằng cuộc sống luôn là mục tiêu mà ta hướng tới';
                $typeNote[] = 'Hãy cùng nhau thực hiện nhé!';
                $typeName = 'Cân bằng cuộc sống và tiết kiệm';
                $planData = [
                    'nhu_cau_co_ban' => $charge * 0.5,
                    'giai_tri' => $charge * 0.25,
                    'tiet_kiem' => $charge * 0.15,
                    'khac' => $charge * 0.1,
                    'note'  => 'Người muốn vừa có tài chính ổn định, vừa có thể tận hưởng cuộc sống mà không ảnh hưởng đến tương lai.',
                    'advantage' => [
                        "Giữ được sự ổn định tài chính và vẫn có thể tận hưởng cuộc sống.",
                        "Có cả quỹ tiết kiệm lẫn ngân sách giải trí hợp lý.",
                        "Dễ duy trì lâu dài mà không cảm thấy áp lực.",
                    ],
                    'disadvantage' => [
                        "Tích lũy tài chính chậm hơn so với kế hoạch tiết kiệm tối đa.",
                        "Nếu thu nhập thấp, có thể cảm thấy chưa đủ linh hoạt.",
                        "Cần kỷ luật để không lấn sang khoản chi tiêu khác.",
                    ]
                ];
                break;
        }

        return view('pages.idea_plan', compact('titlePage', 'planData', 'typeName', 'typeNote', 'evaluateNote'));
    }

    public function listSearch(Request $request)
    {
        $userId = auth()->user()->id;
        $type = $request->input('type') ?? null;
        $search = $request->input('search');
        $date = $request->input('date');
        $expenseCategoryFilter = $request->input('expense_category');
        $incomeCategoryFilter = $request->input('income_category');
    
        // Lấy danh mục chi tiêu và thu nhập
        $mExpenses = MExpense::where('user_id', $userId)->get();
        $mIncomes = MIncome::where('user_id', $userId)->get();
    
        // Khởi tạo danh sách rỗng
        $dataExpenses = collect();
        $dataIncomes = collect();
    
        // Nếu có lọc theo danh mục chi tiêu hoặc không có chọn danh mục nào, lấy dữ liệu chi tiêu
        if ($type == 'expense' || !$type) {
            if (!empty($expenseCategoryFilter) || (empty($expenseCategoryFilter) && empty($incomeCategoryFilter))) {
                $queryExpenses = Expense::where('user_id', $userId);
        
                // Nếu có từ khóa tìm kiếm, lọc theo tên
                if (!empty($search)) {
                    $queryExpenses->where('name', 'like', "%$search%");
                }
        
                // Nếu có lọc theo ngày
                if (!empty($date)) {
                    $queryExpenses->whereDate('created_at', $date);
                }
        
                // Nếu có lọc theo danh mục chi tiêu
                if (!empty($expenseCategoryFilter)) {
                    $queryExpenses->where('m_expense_id', $expenseCategoryFilter);
                }
        
                // Lấy danh sách chi tiêu
                $dataExpenses = $queryExpenses->orderBy('date', 'desc')->get()->map(function ($item) {
                    $item->type = 'expense';
                    return $item;
                });
            }
        }
    
        // Nếu có lọc theo danh mục thu nhập hoặc không có chọn danh mục nào, lấy dữ liệu thu nhập
        if ($type == 'income' || !$type) {
            if (!empty($incomeCategoryFilter) || (empty($expenseCategoryFilter) && empty($incomeCategoryFilter))) {
                $queryIncomes = Income::where('user_id', $userId);
        
                // Nếu có từ khóa tìm kiếm, lọc theo tên
                if (!empty($search)) {
                    $queryIncomes->where('name', 'like', "%$search%");
                }
        
                // Nếu có lọc theo ngày
                if (!empty($date)) {
                    $queryIncomes->whereDate('created_at', $date);
                }
        
                // Nếu có lọc theo danh mục thu nhập
                if (!empty($incomeCategoryFilter)) {
                    $queryIncomes->where('m_income_id', $incomeCategoryFilter);
                }
        
                // Lấy danh sách thu nhập
                $dataIncomes = $queryIncomes->orderBy('date', 'desc')->get()->map(function ($item) {
                    $item->type = 'income';
                    return $item;
                });
            }
        }
    
        // Gộp danh sách thu nhập & chi tiêu
        $data = $dataExpenses->concat($dataIncomes);
        $extendClass = 'disable-scroll';
        return view('pages.home-search', compact('type', 'extendClass', 'data', 'mExpenses', 'mIncomes'));
    }
    
}
