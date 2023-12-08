/**
 * Start time zone widget script
 */

(function ($, elementor) {
    'use strict';
    var widgetTimeZone = function ($scope, $) {
        var $TimeZone = $scope.find('.bdt-time-zone');

        if (!$TimeZone.length) {
            return;
        }

        elementorFrontend.waypoint($TimeZone, function () {
            var $this = $(this),
                $settings = $this.data('settings'),
                timeFormat,
                offset = $settings.gmt,
                dateFormat = $settings.dateFormat;

            var timeZoneApp = {
                digitalClock: function () {
                    if ($settings.timeHour == '12h') {
                        timeFormat = '%I:%M:%S %p';
                    } else {
                        timeFormat = '%H:%M:%S';
                    }
                    var dateFormat = $settings.dateFormat;
                    if (dateFormat != 'emptyDate') {
                        dateFormat = '<div class=\"bdt-time-zone-date\"> ' + $settings.dateFormat + ' </div>'
                    } else {
                        dateFormat = '';
                    }
                    var country;
                    if ($settings.country != 'emptyCountry') {
                        country = '<div  class=\"bdt-time-zone-country\">' + $settings.country + '</div>';
                    } else {
                        country = ' ';
                    }
                    var timeZoneFormat = '<div class=\"bdt-time-zone-dt\"> ' + country + ' ' + dateFormat + ' <div class=\"bdt-time-zone-time\">' + timeFormat + ' </div> </div>';

                    if (offset == '') return;
                    var options = {
                        format: timeZoneFormat,
                        timeNotation: $settings.timeHour, //'24h',
                        am_pm: true,
                        utc: (offset == 'local') ? false : true,
                        utcOffset: (offset == 'local') ? null : offset,
                    }

                    $('#' + $settings.id).jclock(options);
                },
                convertToTimeZoneAndFormat: function (date, offset) {
                    // Get the UTC time in milliseconds
                    const utcTime = date.getTime() + (date.getTimezoneOffset() * 60000);

                    // Calculate the target time using the offset
                    const targetTime = new Date(utcTime + (offset * 3600000));

                    // Extract hours, minutes, and seconds
                    let hours = targetTime.getHours(),
                        minutes = targetTime.getMinutes(),
                        seconds = targetTime.getSeconds();
                    const ampm = hours >= 12 ? 'PM' : 'AM',
                        getDate = targetTime.toDateString();
                    hours = hours % 12 || 12; // Convert to 12-hour format and handle midnight (0 AM)

                    // Add leading zeros to single-digit minutes and seconds
                    minutes = minutes < 10 ? '0' + minutes : minutes;
                    seconds = seconds < 10 ? '0' + seconds : seconds;

                    return {
                        hours,
                        minutes,
                        seconds,
                        ampm,
                        getDate,
                    };
                },
                formatDate: function (inputDate, formatOption) {
                    var date = new Date(inputDate),
                        selectedFormat = formatOption;

                    if (!selectedFormat) {
                        console.error('Invalid format option');
                        return '';
                    }

                    // Replace format placeholders
                    var formattedDate = selectedFormat.replace(/%([a-zA-Z])/g, function (_, formatCode) {
                        switch (formatCode) {
                            case 'd':
                                return String(date.getDate()).padStart(2, '0');
                            case 'm':
                                return String(date.getMonth() + 1).padStart(2, '0');
                            case 'y':
                                return String(date.getFullYear()).slice(-2);
                            case 'Y':
                                return String(date.getFullYear());
                            case 'b':
                                return date.toLocaleString('default', {
                                    month: 'short'
                                });
                            case 'a':
                                return date.toLocaleString('default', {
                                    weekday: 'short'
                                });
                            default:
                                return formatCode;
                        }
                    });

                    return formattedDate;
                },
                date: function () {
                    let localDate = new Date(),
                        targetOffset = offset,
                        result = timeZoneApp.convertToTimeZoneAndFormat(localDate, targetOffset),
                        date = result.getDate;

                    const formattedDate = this.formatDate(date, dateFormat);
                    $($TimeZone).find('.bdt-time-zone-date').text(formattedDate);
                },
                updateTime: function () {
                    setInterval(function () {
                        let localDate = new Date(),
                            targetOffset = offset,
                            result = timeZoneApp.convertToTimeZoneAndFormat(localDate, targetOffset);

                        let second = result.seconds * 6,
                            minute = result.minutes * 6 + second / 60,
                            hour = ((result.hours % 12) / 12) * 360 + 90 + minute / 12;

                        $($TimeZone).find('.bdt-clock-hour').css("transform", "rotate(" + hour + "deg)");
                        $($TimeZone).find('.bdt-clock-minute').css("transform", "rotate(" + minute + "deg)");
                        $($TimeZone).find('.bdt-clock-second').css("transform", "rotate(" + second + "deg)");
                        $($TimeZone).find('.bdt-clock-am-pm').text(result.ampm);

                    }, 1000);

                    this.date();
                },
                init: function () {
                    if ('digital' == $settings.clock_style) {
                        this.digitalClock();
                    } else {
                        this.updateTime();
                    }
                }
            }
            timeZoneApp.init();
        }, {
            offset: 'bottom-in-view'
        });
    };
    jQuery(window).on('elementor/frontend/init', function () {
        elementorFrontend.hooks.addAction('frontend/element_ready/bdt-time-zone.default', widgetTimeZone);
    });
}(jQuery, window.elementorFrontend));

/**
 * End time zone widget script
 */