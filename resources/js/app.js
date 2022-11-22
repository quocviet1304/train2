require('./bootstrap');
const app = {
    model_kana_prefix : [
        '456'
    ],
    model_name_prefix : [],
    model_displacement : [],
    model_marker : [],
    loadData(page = 1)
    {
        let cars = $('#cars');
        let categoryColumn = cars.data('category');

        $.ajax({
            url: '/ajax/getProducts',
            headers: {
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr(
                    "content"
                ),
            },
            type: "POST",
            dataType: "json",
            data: { categoryColumn, page },
            beforeSend: function () {
                $('.overload').removeClass('hidden');
            },
            success: function (result) {
                cars.children('.list-card').html(result)
                $('.overload').addClass('hidden');
            },
            error: function(xhr, ajaxOptions, thrownError) {
                console.log(xhr.status)
                console.log(thrownError)
            }
        });
    },
    filter(){
        $(document).on("click", '.action-filter',function (){
            if(!$(this).hasClass('disable')){
                $(this).toggleClass('active');
                let value = $(this).data('val');
                let typeFilter = $(this).parents('.group-input').data('model');
                app[typeFilter].push(value);
                app.loadData()
            }
        });
    }
}

$(document).ready(function (){
    app.loadData();
    app.filter();
})

