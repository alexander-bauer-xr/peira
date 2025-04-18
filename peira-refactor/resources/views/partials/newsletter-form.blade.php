<div class="emailregister">
    <form method="post" action="" enctype="multipart/form-data" id="catform" style="margin-right:35px;">
        <div id="labelnews">
            <p>
                <label for="adressfield">e-Mail</label><br>
                <input type="text" name="postEmailNews" id="adressfield">
            </p>
            <div class="name-container">
                <p>
                    <label for="namefield">{{ __('content.vorname') }}</label><br>
                    <input type="text" name="postNameNews" id="namefield">
                </p>
                <p>
                    <label for="nachnamefield">{{ __('content.nachname') }}</label><br>
                    <input type="text" name="postNachNameNews" id="nachnamefield">
                </p>
            </div>
            <p>
                <input type="checkbox" id="postAgree" name="agree" style="width:25px;" value="agreed">
                <label id="versandtzu">{{ __('content.agree_text') }}</label>
            </p>
            <a id="newadress" onclick="subscribenews();">{{ __('content.subscribe') }}</a>
        </div>
    </form>
    <div id="abo" class="abonnieren">{{ __('content.infomail') }}</div>
</div>
