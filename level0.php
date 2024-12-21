<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>English Vocabulary Practice</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .fixed-button {
            position: fixed;
            top: 0;
            left: 10%;
            padding: 10px 20px;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
    </style>

</head>
<body>
<div class="container mt-5 p-5" style="padding-bottom: 200px">
    <h2 class="mb-4">English Vocabulary Practice</h2>
    <!-- Select Lesson -->
    <a href="level0.php">Level 1</a>

    <div class="mb-3">
        <label for="lessonSelect" class="form-label">Choose Lesson</label>
        <select class="form-select" id="lessonSelect">
            <option value="lesson2">Lesson 2 - countable nouns & unicountable nouns (danh từ đếm được, danh từ không đếm được)</option>
            <option value="lesson3">Lesson 3 - adjectives</option>
            <option value="lesson4">Lesson 4 - adverbs</option>
            <option value="lesson5">Lesson 5 - the comparative</option>
            <option value="lesson6">Lesson 6 - the superlative</option>
            <option value="lesson7">Lesson 7 - enough/too</option>
            <option value="lesson8">Lesson 8 - conjunctions</option>
            <option value="lesson9">Lesson 9 - air travel - a/an/the</option>
            <option value="lesson10">Lesson 10 - passive voice</option>
            <option value="lesson11">Lesson 11 - if</option>
            <option value="lesson12">Lesson 12 - To Vo</option>
            <option value="lesson13">Lesson 13 - Ving</option>
            <option value="lesson14">Lesson 14 - Mệnh đê quan hệ</option>
            <option value="lesson15">Lesson 15 - phrasal verbs</option>
        </select>
    </div>

    <!-- Select Study Mode -->
    <div class="mb-3">
        <label for="studyModeSelect" class="form-label">Choose Study Mode</label>
        <select class="form-select" id="studyModeSelect">
            <option value="englishToVietnamese">Học từ vựng bằng cách điền nghĩa tiếng Việt</option>
            <option value="vietnameseToEnglish">Học từ vựng bằng cách điền nghĩa tiếng Anh</option>
            <option value="imageToWord">Học từ vựng theo nhìn ảnh</option>
        </select>
    </div>
    <button class="btn btn-primary fixed-button mt-3" id="submitBtn">Check results</button>
    <!-- Submit Button -->
    <!-- Vocabulary Table -->
    <table class="table table-bordered">
        <thead>
        <tr id="tableHeader">
            <th>English Word</th>
            <th>Your Translation</th>
        </tr>
        </thead>
        <tbody id="vocabularyTable">
        <!-- Rows will be dynamically inserted here -->
        </tbody>
    </table>


</div>

<!-- jQuery (required for AJAX) -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script>
    $(document).ready(function () {
        // Function to load data when lesson or study mode is changed
        function loadVocabulary() {
            var lesson = $('#lessonSelect').val();
            var studyMode = $('#studyModeSelect').val();

            // Send AJAX request to PHP to get vocabulary data
            $.ajax({
                url: 'level0/get_vocabulary.php',
                type: 'GET',
                data: {
                    lesson: lesson,
                    studyMode: studyMode
                },
                success: function (response) {
                    var data = JSON.parse(response);
                    var tableHeader = $('#tableHeader');
                    var tableBody = $('#vocabularyTable');
                    tableBody.empty();

                    // Depending on study mode, update table header and rows
                    if (studyMode === 'englishToVietnamese') {
                        tableHeader.html('<th>English Word</th><th>Your Translation</th>');
                        data.forEach(function (item, index) {
                            tableBody.append(
                                '<tr><td>' + item.english_word + '</td>' +
                                '<td><input type="text" class="form-control" id="input-' + index + '">' +
                                '<div id="error-' + index + '" class="text-danger mt-1" style="display:none;"></div></td></tr>'
                            );
                        });
                    } else if (studyMode === 'vietnameseToEnglish') {
                        tableHeader.html('<th>Vietnamese Word</th><th>Your Translation</th>');
                        data.forEach(function (item, index) {
                            tableBody.append(
                                '<tr><td>' + item.translations[0] + '</td>' +
                                '<td><input type="text" class="form-control" id="input-' + index + '">' +
                                '<div id="error-' + index + '" class="text-danger mt-1" style="display:none;"></div></td></tr>'
                            );
                        });
                    } else if (studyMode === 'imageToWord') {
                        tableHeader.html('<th>Image</th><th>Your Translation</th>');
                        data.forEach(function (item, index) {
                            tableBody.append(
                                '<tr><td><img src="' + item.image + '" alt="' + item.english_word + '" style="width:100px;"></td>' +
                                '<td><input type="text" class="form-control" id="input-' + index + '">' +
                                '<div id="error-' + index + '" class="text-danger mt-1" style="display:none;"></div></td></tr>'
                            );
                        });
                    }
                }
            });
        }

        // Trigger loadVocabulary when lesson or study mode changes
        $('#lessonSelect, #studyModeSelect').change(loadVocabulary);

        // Initial load
        loadVocabulary();

        // Handle submit action
        $('#submitBtn').click(function () {
            var lesson = $('#lessonSelect').val();
            var studyMode = $('#studyModeSelect').val();
            var inputs = [];

            // Collect all user inputs
            $('#vocabularyTable tr').each(function (index) {
                var inputVal = $(this).find('input').val();
                inputs.push({index: index, value: inputVal});
            });

            // Send the inputs to the server for validation
            $.ajax({
                url: 'level0/post_vocabulary.php',
                type: 'POST',
                data: {
                    lesson: lesson,
                    studyMode: studyMode,
                    inputs: JSON.stringify(inputs)
                },
                success: function (response) {
                    var results = JSON.parse(response);
                    results.forEach(function (result) {
                        var inputField = $('#input-' + result.index);
                        var errorDiv = $('#error-' + result.index);

                        if (result.correct) {
                            inputField.addClass('border border-success');
                            inputField.removeClass('border border-danger');
                            errorDiv.hide(); // Ẩn thông báo lỗi nếu trả lời đúng
                        } else {
                            inputField.addClass('border border-danger');
                            inputField.removeClass('border border-success');
                            errorDiv.text('Correct answers: ' + result.correctAnswer).show(); // Hiển thị các đáp án đúng
                        }
                    });
                }
            });
        });
    });
</script>
</body>
</html>
