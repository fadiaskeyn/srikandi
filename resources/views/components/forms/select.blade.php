@props([
    'id' => '',
    'name' => '',
    'value' => '',
    'class' => 'bg-gray-200 appearance-none border-2 border-gray-200 rounded-full w-full py-2 px-4 text-gray-700 leading-tight focus:outline-none focus:bg-white',
    'label',
    'labelClass' => 'block text-gray-500 font-bold mx-auto md:text-left mb-1 md:mb-0',
    'options',
    'selected' => null,
])

<div class="md:w-1/3">
    <label {{ $attributes->merge(['class' => $labelClass]) }} for="{{ $name }}">
        {{ $label }}
    </label>
</div>
<div class="md:w-2/3 relative">
    <select @if(strlen($name) > 0) name="{{ $name }}" @endif id="{{ $id }}" {{ $attributes->merge(['class' => $class]) }}>
        <option selected disabled>Pilih salah satu</option>
        @foreach ($options as $key => $option)
            <option value="{{ $key }}" {{ $option == $selected ? 'selected' : '' }}>{{ $option }}</option>
        @endforeach
    </select>
    <span class="absolute right-4 top-2"><iconify-icon icon="ep:arrow-down-bold"></iconify-icon></span>
</div>

@error($name)
    <div class="text-red-500 mt-2 text-sm">
        {{ $message }}
    </div>
@enderror
