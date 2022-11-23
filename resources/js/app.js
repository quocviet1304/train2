require('./bootstrap');
const app = {
    page_default : 1,
    model_kana_prefix : [],
    model_name_prefix : [],
    model_displacement : [],
    model_maker_code : [],
    loadData(update = false)
    {
        let cars = $('#cars');
        let categoryColumn = cars.data('category');
        if(!update) app.page_default = 1;

        $.ajax({
            url: '/ajax/getProducts',
            headers: {
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr(
                    "content"
                ),
            },
            type: "POST",
            dataType: "json",
            data: {
                categoryColumn,
                page: app.page_default,
                model_kana_prefix: app.model_kana_prefix,
                model_name_prefix: app.model_name_prefix,
                model_displacement: app.model_displacement,
                model_maker_code: app.model_maker_code,
            },
            success: function (result) {

                if (update) {
                    if (result) cars.children('.list-card').append(result)
                } else {
                    cars.children('.list-card').html(result)
                }

            },
            error: function(xhr, ajaxOptions, thrownError) {
                console.log(xhr.status)
                console.log(thrownError)
            }
        });

    },
    filter()
    {
        $(document).on("click", '.action-filter',function (){
            if(!$(this).hasClass('disable')){

                let value = $(this).data('val');
                let typeFilter = $(this).parents('.group-input').data('model');

                if ($(this).hasClass('active')) {
                    let index = app[typeFilter].findIndex( item => item === value );
                    if(index > -1){
                        app[typeFilter].splice(index, 1)
                    }
                } else {
                    app[typeFilter].push(value);
                }

                if (typeFilter === 'model_kana_prefix') {
                    app.model_name_prefix = [];
                    $('.filter .group-input[data-model=model_name_prefix]').children().children().removeClass('active');
                } else if(typeFilter === 'model_name_prefix') {
                    app.model_kana_prefix = [];
                    $('.filter .group-input[data-model=model_kana_prefix]').children().children().removeClass('active');
                }

                $(this).toggleClass('active');
                app.loadData()
            }
        });

        $(document).on("click", ".reset-all", function (){
            app.model_kana_prefix = [];
            app.model_name_prefix = [];
            app.model_displacement = [];
            app.model_maker_code = [];
            app.page_default = 1;
            $('.action-filter').removeClass('active');
            app.loadData()
        })

    },
    scrollLoad()
    {
        $(document).on('scroll', function () {
            if(($(window).scrollTop() + $(window).height()) >= $(document).height()) {
                app.page_default++
                app.loadData(true)
            }
        })
    },
    handleCheckbox(){

    }
}

$(document).ready(function () {
    app.loadData();
    app.filter();
    app.scrollLoad()
})

