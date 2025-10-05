<div class="textkarte">
    <div>
        <div class="textkartehead">{{ $zitat->localizedTitle($locale) }}</div>
    @replaceVideo($zitat->localizedBody($locale))
    </div>
</div>