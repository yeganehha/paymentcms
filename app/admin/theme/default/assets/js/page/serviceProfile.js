function add() {
    var string = $('#typeOfAddItem').html();
    string = string.replace(new RegExp('__IIDD__', 'g'), numberOfElement);
    $('#moreFields').append(string);
    numberOfElement++;
}