$.timepicker.regional['de'] = {
    timeOnlyTitle: 'Uhrzeit ausw\u00e4hlen',
    timeText: 'Zeit',
    hourText: 'Stunde',
    minuteText: 'Minute',
    secondText: 'Sekunde',
    currentText: 'Jetzt',
    closeText: 'Ausw\u00e4hlen',
    ampm: false
};
$.timepicker.setDefaults($.timepicker.regional['de']);

$.datepicker.regional['de'] = {clearText: 'l\u00f6schen', clearStatus: 'aktuelles Datum l\u00f6schen',
    closeText: 'schließen', closeStatus: 'ohne \u00c4nderungen schließen',
    prevText: '<zur\u00fcck', prevStatus: 'letzten Monat zeigen',
    nextText: 'Vor>', nextStatus: 'n\u00e4chsten Monat zeigen',
    currentText: 'heute', currentStatus: '',
    monthNames: ['Januar','Februar','M\u00e4rz','April','Mai','Juni',
        'Juli','August','September','Oktober','November','Dezember'],
    monthNamesShort: ['Jan','Feb','M\u00e4r','Apr','Mai','Jun',
        'Jul','Aug','Sep','Okt','Nov','Dez'],
    monthStatus: 'anderen Monat anzeigen', yearStatus: 'anderes Jahr anzeigen',
    weekHeader: 'Wo', weekStatus: 'Woche des Monats',
    dayNames: ['Sonntag','Montag','Dienstag','Mittwoch','Donnerstag','Freitag','Samstag'],
    dayNamesShort: ['So','Mo','Di','Mi','Do','Fr','Sa'],
    dayNamesMin: ['So','Mo','Di','Mi','Do','Fr','Sa'],
    dayStatus: 'Setze DD als ersten Wochentag', dateStatus: 'Wähle D, M d',
    dateFormat: 'dd.mm.yy', firstDay: 1,
    initStatus: 'W\u00e4hle ein Datum', isRTL: false};
$.datepicker.setDefaults($.datepicker.regional['de']);