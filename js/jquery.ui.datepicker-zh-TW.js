/* Chinese initialisation for the jQuery UI date picker plugin. */
/* Written by Ressol (ressol@gmail.com). */
jQuery(function($){
	$.datepicker.regional['zh-TW'] = {
		closeText: '確定',
		prevText: '&#x3C;上月',
		nextText: '下月&#x3E;',
		currentText: '本月',
		monthNames: ['一月','二月','三月','四月','五月','六月',
		'七月','八月','九月','十月','十一月','十二月'],
		monthNamesShort: ['一月','二月','三月','四月','五月','六月',
		'七月','八月','九月','十月','十一月','十二月'],
		dayNames: ['星期日','星期一','星期二','星期三','星期四','星期五','星期六'],
		dayNamesShort: ['周日','周一','周二','周三','周四','周五','周六'],
		dayNamesMin: ['日','一','二','三','四','五','六'],
		weekHeader: '周',
		dateFormat: 'yy/mm/dd',
		firstDay: 1,
		isRTL: false,
		showMonthAfterYear: true,
		yearSuffix: ''};
	$.datepicker.setDefaults($.datepicker.regional['zh-TW']);

	var old_generateMonthYearHeader = $.datepicker._generateMonthYearHeader;
    var old_get = $.datepicker._get;
    $.extend($.datepicker, {
        _generateMonthYearHeader:function (a,b,c,d,e,f,g,h) {
            var htmlYearMonth = old_generateMonthYearHeader.apply(this, [a, b, c, d, e, f, g, h]);
            if ($(htmlYearMonth).find(".ui-datepicker-year").length > 0) {
                htmlYearMonth = $(htmlYearMonth).find(".ui-datepicker-year").find("option").each(function (i, e) {
                    if (Number(e.value) - 1911 > 0) $(e).text(Number(e.innerText) - 1911 + '年');
                }).end().end().get(0).outerHTML;
            }
            return htmlYearMonth;
        },
        _get:function (a, b) {
            a.selectedYear = a.selectedYear - 1911 < 0 ? a.selectedYear + 1911 : a.selectedYear;
            a.drawYear = a.drawYear - 1911 < 0 ? a.drawYear + 1911 : a.drawYear;
            a.curreatYear = a.curreatYear - 1911 < 0 ? a.curreatYear + 1911 : a.curreatYear;
            return old_get.apply(this, [a, b]);
        },
        _setDateDatepicker: function (a, b) {
            if (a = this._getInst(a)) { this._setDate(a, b); this._updateDatepicker(a); this._updateAlternate(a) }
        },
        _widgetDatepicker: function () {
            return this.dpDiv
        }
    });
});
