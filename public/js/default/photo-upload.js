$(document).ready(function () {
    $('form').on('change', 'input[name^=photo]', function (event) {
        event.preventDefault();
        if ($('input[name^=photo]:last').val() != '') {
            let newButton = '<tr><td><input type="file" name="photo[]" multiple="multiple"></td></tr>';
            $(this).parent().parent().parent().append(newButton);
        }
    });
})