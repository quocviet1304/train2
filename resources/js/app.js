require('./bootstrap');
const app = {
    page_default : 1,
    model_kana_prefix : [],
    model_name_prefix : [],
    model_displacement : [],
    model_maker_code : [],
    model_code: {
        code: [],
        maker: []
    },
    filter : [
        'model_kana_prefix',
        'model_name_prefix',
        'model_displacement',
        'model_maker_code'
    ],
    loadData(update = false)
    {
        const queryString = window.location.search;
        const urlParams = new URLSearchParams(queryString);
        console.log(urlParams.get('model_displacement'));

        let cars = $('#cars');
        if (cars.length !== 0) {
            let categoryColumn = cars.data('category');
            if(!update) app.page_default = 1;

            let data = {
                categoryColumn,
                page: app.page_default,
            }

            app.filter.forEach((value, index) => {
                if(urlParams.get(value)) data.push(urlParams.get(value))
            })

            $.ajax({
                url: '/ajax/getProducts',
                headers: {
                    "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr(
                        "content"
                    ),
                },
                type: "POST",
                dataType: "json",
                data: data,
                success: function (result)
                {
                    if (update) {
                        if (result) cars.children('.list-card').append(result)
                    } else {
                        app.model_code.code = [];
                        app.model_code.maker = [];
                        cars.children('.list-card').html(result)
                    }

                },
                error: function(xhr, ajaxOptions, thrownError)
                {
                    console.log(xhr.status)
                    console.log(thrownError)
                }
            });
        }

    },
    filter()
    {
        $(document).on("click", '.action-filter',function (e) {
            if ($(this).hasClass('disable')) {

                e.preventDefault();

                // let value = $(this).data('val');
                // let typeFilter = $(this).parents('.group-input').data('model');
                //
                // if ($(this).hasClass('active')) {
                //     let index = app[typeFilter].findIndex( item => item === value );
                //     if(index > -1){
                //         app[typeFilter].splice(index, 1)
                //     }
                // } else {
                //     app[typeFilter].push(value);
                // }
                //
                // if (typeFilter === 'model_kana_prefix') {
                //     app.model_name_prefix = [];
                //     $('.filter .group-input[data-model=model_name_prefix]').children().children().removeClass('active');
                // } else if(typeFilter === 'model_name_prefix') {
                //     app.model_kana_prefix = [];
                //     $('.filter .group-input[data-model=model_kana_prefix]').children().children().removeClass('active');
                // }
                //
                // $(this).toggleClass('active');
                // app.loadData()
            }
        });

        // $(document).on("click", ".reset-all", function () {
        //     app.model_kana_prefix = [];
        //     app.model_name_prefix = [];
        //     app.model_displacement = [];
        //     app.model_maker_code = [];
        //     app.page_default = 1;
        //     $('.action-filter').removeClass('active');
        //     app.loadData()
        // })

    },
    scrollLoad()
    {
        $(document).on('scroll', function () {
            if (($(window).scrollTop() + $(window).height()) >= $(document).height()) {
                app.page_default++
                app.loadData(true)
            }
        })
    },
    handleChooseCar()
    {
        function removeModelCode(code, maker)
        {
            let codeIndex = app.model_code.code.findIndex( item => item === code);
            let makerIndex = app.model_code.maker.findIndex( item => item === maker);
            if(codeIndex > -1) app.model_code.code.splice(codeIndex, 1);
            if(makerIndex > -1) app.model_code.maker.splice(makerIndex, 1);
        }

        $(document).on('change', '.checkbox-item', function () {
            let code = $(this).data('code')
            let maker = $(this).data('maker')
            if (this.checked) {
                if (app.model_code.code.length >= 10) {
                    alert('一度に10車種まで選択できます。')
                    removeModelCode(code, maker)
                    $(this).prop('checked', '');
                } else {
                    app.model_code.code.push(code)
                    app.model_code.maker.push(maker)
                }
            } else {
                removeModelCode(code, maker)
            }

        })
    },
    handleCheckbox()
    {
        $(document).on('click', '#add-car', function () {
            if (app.model_code.code.length === 0) {
                alert('検索対象の車種を選択してください。')
            } else {
                let urlCode = app.model_code.code.join('_');
                let urlMaker = app.model_code.maker.join('_');
                console.log([urlCode, urlMaker])
                location.href = `/area?mmc=${urlMaker}?moc=${urlCode}`
            }
        })
    }
}

$(document).ready(function () {
    // app.loadData();
    app.filter();
    app.scrollLoad()
    app.handleCheckbox()
    app.handleChooseCar()
})

