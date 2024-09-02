var DateUtils = function(servertime) {
    var me = this;

    me.servernow = servertime;
    me.diffServerTime = 0;

    init = function(){
        if(me.servernow != undefined && me.servernow) {
            var now = me.parse(me.servernow);
            me.diffServerTime = now - new Date().getTime();
        }
    }

    me.parse = function(datetime){
        if(datetime){
            var now  = new Date();
            var date = now.format('Y-m-d');
            var time = '00:00:00';

            let dt = datetime.split(' ');
                dt = (dt.length > 1) ? dt : datetime.split('T');
            if(dt.length > 1){
                date = dt[0];
                time = dt[1];
            }
            else {
                if(dt[0].split('-').length > 1) date = dt[0];
                else if(dt[0].split(':').length > 1) time = dt[0];
                else if(dt[0].split('.').length > 1) time = dt[0].split('.').join(':');
            }
            return new Date(date + 'T' + time.substr(0,8) +'.000+07:00');
        }
        return new Date();
    };

    me.parseTime = function(time){
        var now = new Date();
        if(me.servernow != undefined && me.servernow) now = me.parse(me.servernow);

        var y = now.getFullYear();
        var m = now.getMonth();
        var d = now.getDate();

        var h = time.substr(0, 2);  h = h ? h : '00';
        var i = time.substr(3, 2);  i = i ? i : '00';
        var s = time.substr(6, 2);  s = s ? s : '00';

        if(m < 10) m = '0'+m;
        if(d < 10) d = '0'+d;

        return me.parse(y+'-'+m+'-'+d+' '+h+':'+i+':'+s);
    };

    me.serverTime = function(){
        var diff = me.diffServerTime;
        var time = new Date(new Date().getTime() + diff);
        return time;
    };

    me.diff = function(date1, date2){
        if(typeof date1 == 'string') date1 = (date1.length >= 10) ? me.parse(date1) : me.parseTime(date1);
        if(typeof date2 == 'string') date2 = (date2.length >= 10) ? me.parse(date2) : me.parseTime(date2);
        var distance = date2 - date1;
        var diff = distance;

        //if(distance < 0) diff = diff * -1;

        var d = Math.floor(diff / (1000 * 60 * 60 * 24));
        var h = Math.floor((diff % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
        var m = Math.floor((diff % (1000 * 60 * 60)) / (1000 * 60));
        var s = Math.floor((diff % (1000 * 60)) / 1000);

        h = (h < 10) ? ('0'+h) : h;
        m = (m < 10) ? ('0'+m) : m;
        s = (s < 10) ? ('0'+s) : s;
        return {day: d, hour: h, minute: m, second: s, distance: distance};
    };

    me.diffServer = function(time){
        return me.diff(time, me.serverTime());
    };

    me.format = function(date, dateformat){
        date = date.replace("T", " ");
        date = date.substr(0,19);
        if(typeof date == 'string') date = me.parse(date);
        if(dateformat == undefined) dateformat = 'd/m/Y';
        return date.format(dateformat);
    }

    me.shortDay = function(date){
        let d = me.diffServer(date+' 23:59:59');
        if(d.day > 360 || d.day < -360) return Math.ceil(d.day/360) + 'y';
        else if(d.day > 30 || d.day < -30) return Math.ceil(d.day/30) + 'm';
        else if(d.day == 0) return 'Today';
        return d.day+'d';
    }


    init();
}


/**************************************
 * Date class extension
 *
 */
    // Provide month names
    Date.prototype.getMonthName = function(){
        var month_names = [
                            'January',
                            'February',
                            'March',
                            'April',
                            'May',
                            'June',
                            'July',
                            'August',
                            'September',
                            'October',
                            'November',
                            'December'
                        ];

        return month_names[this.getMonth()];
    }

    // Provide month abbreviation
    Date.prototype.getMonthAbbr = function(){
        var month_abbrs = [
                            'Jan',
                            'Feb',
                            'Mar',
                            'Apr',
                            'May',
                            'Jun',
                            'Jul',
                            'Aug',
                            'Sep',
                            'Oct',
                            'Nov',
                            'Dec'
                        ];

        return month_abbrs[this.getMonth()];
    }

    // Provide full day of week name
    Date.prototype.getDayFull = function(){
        var days_full = [
                            'Sunday',
                            'Monday',
                            'Tuesday',
                            'Wednesday',
                            'Thursday',
                            'Friday',
                            'Saturday'
                        ];
        return days_full[this.getDay()];
    };

    // Provide full day of week name
    Date.prototype.getDayAbbr = function(){
        var days_abbr = [
                            'Sun',
                            'Mon',
                            'Tue',
                            'Wed',
                            'Thur',
                            'Fri',
                            'Sat'
                        ];
        return days_abbr[this.getDay()];
    };

    // Provide the day of year 1-365
    Date.prototype.getDayOfYear = function() {
        var onejan = new Date(this.getFullYear(),0,1);
        return Math.ceil((this - onejan) / 86400000);
    };

    // Provide the day suffix (st,nd,rd,th)
    Date.prototype.getDaySuffix = function() {
        var d = this.getDate();
        var sfx = ["th","st","nd","rd"];
        var val = d%100;

        return (sfx[(val-20)%10] || sfx[val] || sfx[0]);
    };

    // Provide Week of Year
    Date.prototype.getWeekOfYear = function() {
        var onejan = new Date(this.getFullYear(),0,1);
        return Math.ceil((((this - onejan) / 86400000) + onejan.getDay()+1)/7);
    }

    // Provide if it is a leap year or not
    Date.prototype.isLeapYear = function(){
        var yr = this.getFullYear();

        if ((parseInt(yr)%4) == 0){
            if (parseInt(yr)%100 == 0){
                if (parseInt(yr)%400 != 0){
                    return false;
                }
                if (parseInt(yr)%400 == 0){
                    return true;
                }
            }
            if (parseInt(yr)%100 != 0){
                return true;
            }
        }
        if ((parseInt(yr)%4) != 0){
            return false;
        }
    };

    // Provide Number of Days in a given month
    Date.prototype.getMonthDayCount = function() {
        var month_day_counts = [
                                    31,
                                    this.isLeapYear() ? 29 : 28,
                                    31,
                                    30,
                                    31,
                                    30,
                                    31,
                                    31,
                                    30,
                                    31,
                                    30,
                                    31
                                ];

        return month_day_counts[this.getMonth()];
    }

    // format provided date into this.format format
    Date.prototype.format = function(dateFormat){
        // break apart format string into array of characters
        dateFormat = dateFormat.split("");

        var date = this.getDate(),
            month = this.getMonth(),
            hours = this.getHours(),
            minutes = this.getMinutes(),
            seconds = this.getSeconds();
        // get all date properties ( based on PHP date object functionality )
        var date_props = {
            d: date < 10 ? '0'+date : date,
            D: this.getDayAbbr(),
            j: this.getDate(),
            l: this.getDayFull(),
            S: this.getDaySuffix(),
            w: this.getDay(),
            z: this.getDayOfYear(),
            W: this.getWeekOfYear(),
            F: this.getMonthName(),
            m: month < 10 ? '0'+(month+1) : month+1,
            M: this.getMonthAbbr(),
            n: month+1,
            t: this.getMonthDayCount(),
            L: this.isLeapYear() ? '1' : '0',
            Y: this.getFullYear(),
            y: this.getFullYear()+''.substring(2,4),
            a: hours > 12 ? 'pm' : 'am',
            A: hours > 12 ? 'PM' : 'AM',
            g: hours % 12 > 0 ? hours % 12 : 12,
            G: hours > 0 ? hours : "12",
            h: hours % 12 > 0 ? hours % 12 : 12,
            H: hours < 10 ? '0'+hours : hours,
            i: minutes < 10 ? '0' + minutes : minutes,
            s: seconds < 10 ? '0' + seconds : seconds
        };

        // loop through format array of characters and add matching data else add the format character (:,/, etc.)
        var date_string = "";
        for(var i=0;i<dateFormat.length;i++){
            var f = dateFormat[i];
            if(f.match(/[a-zA-Z]/g)){
                date_string += date_props[f] ? date_props[f] : '';
            } else {
                date_string += f;
            }
        }

        return date_string;
    };
/*
 *
 * END - Date class extension
 *
 ************************************/
