<div class="app-wrapper-footer fixed-footer">
    <div class="app-footer">
        <div class="app-footer__inner">
            <div class="app-footer-left">
                <a href="{{ config('site.web') }}">{{ config('site.title') }} ({{ config('site.subtitle') }})</a>
                &nbsp;
                {{ config('site.company') }}.
                <div class="float-right d-none d-sm-inline-block">
                    <b>Version</b> {{ config('site.version') }}
                </div>

            </div>

            <div class="app-footer-right">
                <b>Copyright &copy; {{ config('site.year') }}.</b>
            </div>
        </div>
    </div>
</div>
