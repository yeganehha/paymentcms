function add() {
    var string = $('#typeOfAddItem').html();
    string = string.replace(new RegExp('__IIDD__', 'g'), numberOfElement);
    string = string.replace(new RegExp('selectpickerTemp', 'g'), 'selectpicker');
    string = string.replace(new RegExp('tagsinputTemp', 'g'), 'tagsinput');
    string = string.replace(new RegExp('tagsinputDataTemp', 'g'), 'data-role="tagsinput" data-color="info"');
    $('#moreFields').append(string);
    numberOfElement++;
    $('.selectpicker').selectpicker('refresh');
    $('.tagsinput').tagsinput('refresh').tagClass('info');
}