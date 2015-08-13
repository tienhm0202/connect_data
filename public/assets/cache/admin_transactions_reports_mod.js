$( document ).ready(function() {
    $('#fromdate').datetimepicker({
        dateFormat: 'yy-mm-dd', 
        timeFormat:'hh:mm:ss', 
        showHour: true, 
        showMinute: true, 
        showSecond: true
    });
    $('#todate').datetimepicker({
        dateFormat: 'yy-mm-dd', 
        timeFormat:'hh:mm:ss', 
        showHour: true, 
        showMinute: true,
        showSecond: true
    });
 
    $('#maintable').stickyTableHeaders({
        fixedOffset: $('#topbar').height() + $('.subnav.navbar-fixed-top').height() + $('.form-inline .well').height()
    });
    
    $('#myModal').on('hidden', function() {
        $(this).removeData('modal');
    });
});
