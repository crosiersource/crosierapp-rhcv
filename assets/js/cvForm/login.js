import $ from "jquery";

$(document).ready(function () {

    let $cpf = $('#cpf');
    let $password = $('#password');
    let $btnEsqueciMinhaSenha = $('#btnEsqueciMinhaSenha');
    let $form = $('#loginForm');


    $btnEsqueciMinhaSenha.click(function() {
        let form = $('<form action="/cvForm/esqueciMinhaSenha" method="post"></form>');
        form.append($('<input></input>').attr("type", "hidden").attr("name", "cpf").val($cpf.val()));
        form.append($('<input></input>').attr("type", "hidden").attr("name", "btnEsqueciMinhaSenha").val("true"));
        $(form).appendTo('body').submit();
    });





});