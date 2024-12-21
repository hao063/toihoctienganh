<?php

if (isset($_POST['lesson']) && isset($_POST['studyMode']) && isset($_POST['inputs'])) {
    // Lấy thông tin từ yêu cầu POST
    $lesson    = $_POST['lesson'];
    $studyMode = $_POST['studyMode'];
    $inputs    = json_decode($_POST['inputs'], true);

    // Xác định file JSON dựa trên bài học
    $jsonFile = $lesson.'.json';

    if (file_exists($jsonFile)) {
        // Đọc nội dung file JSON
        $jsonData       = file_get_contents($jsonFile);
        $vocabularyData = json_decode($jsonData, true);

        $results = [];

        // Hàm để chuẩn hóa chuỗi đầu vào (chuyển thành chữ thường và loại bỏ khoảng trắng)
        function simplifyText($text)
        {
            return strtolower(trim($text)); // Chuyển thành chữ thường và loại bỏ khoảng trắng
        }

        // So sánh từng input với dữ liệu từ file JSON
        foreach ($inputs as $input) {
            $index          = $input['index'];
            $userInput      = simplifyText($input['value']); // Chuẩn hóa input người dùng
            $correctAnswers = [];

            if ($studyMode === 'englishToVietnamese') {
                // Lấy danh sách các câu trả lời đúng và chuẩn hóa
                $correctAnswers = array_map('simplifyText', $vocabularyData[$index]['translations']);
            } else {
                if ($studyMode === 'vietnameseToEnglish') {
                    // Chỉ có 1 đáp án đúng cho tiếng Anh, chuẩn hóa lại
                    $correctAnswers = [simplifyText($vocabularyData[$index]['english_word'])];
                } else {
                    if ($studyMode === 'imageToWord') {
                        // Chỉ có 1 đáp án đúng cho tiếng Anh (học theo ảnh), chuẩn hóa
                        $correctAnswers = [simplifyText($vocabularyData[$index]['english_word'])];
                    }
                }
            }

            // Kiểm tra nếu người dùng nhập đúng với một trong các câu trả lời đúng
            $isCorrect = false;
            foreach ($correctAnswers as $correctAnswer) {
                if (strpos($userInput, $correctAnswer) !== false || strpos($correctAnswer, $userInput) !== false) {
                    $isCorrect = true;
                    break;
                }
            }

            if ($isCorrect) {
                $results[] = ['index' => $index, 'correct' => true];
            } else {
                // Trả về tất cả các câu trả lời đúng để hiển thị cho người dùng biết
                $results[] = [
                    'index'         => $index,
                    'correct'       => false,
                    'correctAnswer' => getResultError($index, $vocabularyData, $studyMode)
                ];
            }
        }

        // Trả về kết quả dưới dạng JSON
        echo json_encode($results);
    }
}

function getResultError(int $index, array $vocabularyData, string $studyMode): string {
    if ($studyMode === 'englishToVietnamese') {
        // Lấy danh sách các câu trả lời đúng và chuẩn hóa
        return implode(', ', $vocabularyData[$index]['translations']);
    } else {
        return $vocabularyData[$index]['english_word'];
    }
}
?>
