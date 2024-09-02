ai.Store = function(option){
    let me = this;

    me.data = [];
    me.totalCount = 0;

    me.start = 0;
    me.limit = 0;
    me.url = null;
    me.method = 'GET';
    me.sort = null;
    me.dir = null;
    me.query = null;
    me.params = {};
    me.dataReader = {data:'data', totalCount:'count'};
    me.mask = false;
    me.loadTpl = null;

    me.error = function(sts, msg){
        ai.toast('<div style="font-size: 18px; color: #ffa19b">' + sts + '</div>' + msg);
    }

    me.init = function(options){
        if(!isNull(options)){
            let opt = options;

            if(!isNull(opt.url)) me.url = opt.url;
            if(!isNull(opt.limit)) me.limit = opt.limit;
            if(!isNull(opt.start)) me.start = opt.start;
            if(!isNull(opt.sort)) me.sort = opt.sort;
            if(!isNull(opt.dir)) me.dir = opt.dir;
            if(!isNull(opt.reader)) me.reader = opt.reader;
            if(!isNull(opt.mask)) me.mask = opt.mask;

            me.params = {
                start: me.start,
                limit: me.limit,
                query: me.query,
                sort: me.sort,
                dir: me.dir,
            };

            if(!isNull(opt.autoLoad)) me.load();
        }
    };

    me.extraParams = function(params){
        me.params = {...me.params, ...params};
    }

    me.ajax = function(action){
        if(me.mask) ai.mask.show();
        me.params.start = me.start;
        $.ajax({
            url: me.url,
            type: me.method,
            data: me.params,
            success: function(result){
                if(me.mask) ai.mask.hide();
                if(typeof action == 'function') action(true, result);
            },
            error: function(error){
                if(me.mask) ai.mask.hide();
                me.error('Error ('+error.status+')', error.statusText);
                if(typeof action == 'function') {
                    action(false, {error: error.status, message: error.statusText})
                }
            }
        });
    }

    me.load = function(action){
        me.start = 0;
        me.ajax(function(success, result){
            if(success){
                let dataReader = me.dataReader.data;
                let countReader = me.dataReader.totalCount;

                me.data = result;
                me.totalCount = me.data.length;
                if(dataReader && countReader){
                    me.data = result[dataReader];
                    me.totalCount = result[countReader];
                }

                me.loadTpl(me.data, me);
                if(typeof action == 'function') action(success, result, me);
            }
            else if(typeof action == 'function') action(success, result, me);
        });
    }

    me.loadMore = function(action){
        me.start = me.data.length;
        me.ajax(function(success, result){
            if(success){
                let dataReader = me.dataReader.data;
                let countReader = me.dataReader.totalCount;

                let data = result;
                me.totalCount += data.length;

                if(dataReader && countReader){
                    data = result[dataReader];
                    me.data = me.data.concat(data);
                    me.totalCount = result[countReader];
                }

                me.loadTpl(data, me, 'append');
                if(typeof action == 'function') action(success, data, me);
            }
            else if(typeof action == 'function') action(success, result, me);
        });
    }


    me.init(option);
}
