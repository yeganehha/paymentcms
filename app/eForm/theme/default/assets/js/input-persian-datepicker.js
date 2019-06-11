$(document).ready(function() {
    var to_persianDatepicker, from_persianDatepicker;
    var to1, from1;
    var persianNumber1 = ["۰","۱","۲","۳","۴","۵","۶","۷","۸","۹"] ;
    to1 = $(".persianDatepicker-to").persianDatepicker({
        altField: '.persianDatepicker-to-alt',
        persianDigit: false ,
        format: 'YYYY/MM/DD ساعت HH:mm',
        title:'ss',
        initialValue: false,
        autoClose: true,
        timePicker: {
            enabled: true,
            meridiem: {
                enabled: false
            },
            second : {
                enabled: false
            },
            minute : {
                enabled: true
            }
        },
        onSelect: function (unix) {
            to1.touched = true;
            if (from1 && from1.options && from1.options.maxDate != unix  ) {
                var cachedValue = from1.getState().selected.unixDate;
                from1.options = {maxDate: unix  };
                if (from1.touched) {
                    from1.setDate(cachedValue);
                }
            }
        }
    });
    from1 = $(".persianDatepicker-from").persianDatepicker({
        altField: '.persianDatepicker-from-alt',
        persianDigit: false ,
        initialValue: false,
        format: 'YYYY/MM/DD ساعت HH:mm',
        timePicker: {
            enabled: true,
            meridiem: {
                enabled: false
            },
            second : {
                enabled: false
            },
            minute : {
                enabled: true
            }
        },
        autoClose: true,
        onSelect: function (unix) {
            from1.touched = true;
            if (to1 && to1.options && to1.options.minDate != unix ) {
                var cachedValue = to1.getState().selected.unixDate;
                to1.options = {minDate: unix};
                if (to1.touched) {
                    to1.setDate(cachedValue);
                }
            }
        }
    });

});