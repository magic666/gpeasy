gplinks.Event_Calendar_Gadget_Nav = function(rel,evt)
{
    evt.preventDefault();
    var text = this.getAttribute('href');
    var qs = text.substr(1); // Remove the question mark
    var href = jPrep(location.href)+'&'+qs;
    $.getJSON(href,ajaxResponse);
}