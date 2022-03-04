function setValidation(){
    $("#searchForm").validate({
    rules: {
        query: {
            required: true,
            minlength: 3
        },
    },
    messages: {
        query: {
            required: "Введите строку для поиска",
            minlength: "Минимальная длина строки поиска - 3 символа"
        },
    },
    errorPlacement: function(error,element) {
        $(element).closest(".input-group").children(".invalid-feedback").html(error)
    },
    highlight: function (element) { 
        $(element).addClass("is-invalid").removeClass("is-valid");
        $(element).closest(".input-group").children(".invalid-feedback").show()
    },
    unhighlight: function (element) {
        $(element).addClass("is-valid").removeClass("is-invalid");
        $(element).closest(".input-group").children(".invalid-feedback").hide()
    }
  });
}

function displayMessage(message)
{
    $('#searchResults').html(message);
}

function displayQueryResultToPage(result)
{
    displayMessage("")
    if(result.length == 0)
    {
        displayMessage("Ничего не найдено")
    }

    result.forEach(element => {
        $( "#searchResults" ).append( `<article><h2>${element.postTitle}</h2><p>${element.commentBody}</p></article>` );
    });
}

function getResult(form)
{
    if(!form.valid()){
        displayMessage("")
        return
    }
    displayMessage("Поиск...")
    var toApi = {
        searchQuery: form.find("#query").val(),
    }
    $.ajax({
        url: '',
        method: 'post',
        data: JSON.stringify(toApi),
        contentType: "application/json; charset=utf-8",
        dataType: "json",
        success: function(data){
            displayQueryResultToPage(data)
        },
        error: function (jqXHR, exception) {
            console.log(exception)
            displayMessage("Ошибка, попробуйте позже")
        }
    });
}

function setOnFormSubmit(){
    $("#searchForm").on("submit", function(e){
        e.preventDefault();
        try {
            getResult($(this));
        } catch (error) {
            console.log(error)
            displayMessage("Ошибка, попробуйте позже")
        }
    });
}

$( document ).ready(function () {
    'use strict'

    setValidation()
    setOnFormSubmit()
})