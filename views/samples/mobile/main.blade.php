@extends('headers.head-onsjs')

@section('content')
    <template id="page1.html">
        <ons-page id="page1">
            <ons-toolbar>
                <div class="left"><ons-back-button>Main</ons-back-button></div>
                <div class="center">Page2</div>
            </ons-toolbar>
            <p>This is the second page.</p>
        </ons-page>
    </template>

    <script>

        ai.require = '*';
        ai.ready(function(){
            var store = ai.create('store', {
                url: '{{ route('sample.data') }}',
                limit: 10,
            });
            var toolbar = ai.create('actionbar', {
                id: 'main-actionbar',
                title: 'Sample',
                store: store,
                action: 'back',
                search: {
                    //store: store,
                    handler: function(val, success, result){console.log(val, success, result)}
                },
                menu:{
                    id: 'main-actionbar-menu',
                    items: [
                        {
                            text: 'Active',
                            icon: 'fa-check-circle-o',
                            handler: function(val, text, index){
                                ai.showPage('page1');
                            }
                        },
                        {text: 'Trashed', icon: 'fa-trash-o'},
                        '-',
                        {
                            id: 'sample',
                            text: 'Samples',
                            icon: ['fa-th', 'fa-external-link'],
                            handler: function(val, obj){ console.log(obj); }
                        },
                        {
                            id: 'sample-submenu',
                            text: 'Submenu',
                            icon: ['fa-gear', 'fa-chevron-right'],
                            items: [
                                { id: 'sample1', text: 'Samples1', icon: 'fa-gear' },
                                { id: 'sample2', text: 'Samples2', icon: 'fa-gear' },
                                {
                                    id: 'sample3',
                                    text: 'Samples3',
                                    icon: ['fa-th', 'fa-external-link'],
                                    handler: function(val, obj){ console.log(obj); },
                                    items: [
                                        { text: 'Samples1', icon: 'fa-gear' },
                                        { text: 'Samples2', icon: 'fa-gear' },
                                    ]
                                },
                            ]
                        },
                        '-',
                        {
                            type: 'filter',
                            //text: 'Filter',
                            //icon: 'fa-filter',
                            //prefixText: 'Filter By',
                            items: [
                                {
                                    id: 'filter-test',
                                    name: 'test',
                                    text: 'Test',
                                    // value: null,
                                    // clearFilterText: 'All',
                                    displayKey: 'name',
                                    valueKey: 'id',
                                    handler: function(val, values, option, obj, succecc, data){
                                        console.log(data);
                                    },
                                    items: [
                                        {id: 1, name: 'Name1'},
                                        {id: 2, name: 'Name2'},
                                        {id: 3, name: 'Name3'},
                                    ],

                                },
                            ]
                        },
                        '-',
                        {
                            type: 'sort',
                            text: 'Sort By',
                            icon: 'fa-sort-amount-asc',
                            items: [
                                {
                                    text: 'Name', sort: 'name',
                                    handler :function(val, item, obj, success, data){
                                        console.log(val);
                                    }
                                },
                                {text: 'Role', sort: 'roleId', dir: 'DESC'},
                            ],
                        },
                    ],
                }
            });
            var listView = ai.create('listview', {
                store: store,
                renderItem: {
                    tpl: `<ons-card>
                              <div class="title">{username}</div>
                              <div class="content">Simple "pull to refresh" functionality to update data.</div>
                          </ons-card>`,
                    renders: [
                        { id: 'id', render: function(rec, val) { return val; } }
                    ],
                },
                loadMore: true,
                pullHook: true,
                itemListener: function(el){

                }
            });

            ai.create('page', {
                id: 'main-page',
                mainPage: true,
                actionBar: toolbar,
                listView: listView,
                intro: true,
            });

            store.load();
        });
    </script>


@endsection


