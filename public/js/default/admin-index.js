$(document).ready(function(){
    function getTdInput(name, maxLen = '', pHolder = '') {
        if (maxLen) maxLen = "maxlength='" + maxLen + "'";
        if (pHolder) pHolder = "placeholder='" + pHolder + "'";
        return "<td><input type='text' name='" + name + "' " + maxLen + pHolder + "/></td>";
    }

    $('#btn-add-user').click(function() {
        $('#tbl-admin-users tbody').append('<tr><td>*</td>' +
            getTdInput('mail[]', 50, 'E-mail') +
            getTdInput('first_name[]', 20, 'First name') +
            getTdInput('description[]', 255, 'Description') +
            getTdInput('birthdate[]', 10, 'Birthdate') +
            getTdInput('password[]', 255, 'New password') +
            '<td></td>' +
            '</tr>');
        $( "input[name^=birthdate]" ).datepicker({
            dateFormat: "dd.mm.yy"
        });
    });

    $( "input[name^=birthdate]" ).datepicker({
        dateFormat: "dd.mm.yy"
    });
});