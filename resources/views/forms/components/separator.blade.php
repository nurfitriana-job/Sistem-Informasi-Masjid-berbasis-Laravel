<x-dynamic-component :component="$getFieldWrapperView()" :field="$field">
    <div x-data="{ state: $wire.$entangle('{{ $getStatePath() }}') }">
        <hr class="border-2 border-gray-300 my-2">
    </div>
</x-dynamic-component>
