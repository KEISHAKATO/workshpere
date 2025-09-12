@props(['name','class'=>'w-5 h-5'])
@switch($name)
    @case('briefcase')
        <svg {{ $attributes->merge(['class'=>$class]) }} viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6">
            <path d="M3 7h18v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V7z"/><path d="M9 7V5a2 2 0 0 1 2-2h2a2 2 0 0 1 2 2v2"/><path d="M3 12h18"/>
        </svg>
    @break
    @case('users')
        <svg {{ $attributes->merge(['class'=>$class]) }} viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6">
            <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/>
            <path d="M22 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/>
        </svg>
    @break
    @case('chat')
        <svg {{ $attributes->merge(['class'=>$class]) }} viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6">
            <path d="M21 15a4 4 0 0 1-4 4H7l-4 4V7a4 4 0 0 1 4-4h10a4 4 0 0 1 4 4z"/>
        </svg>
    @break
    @case('star')
        <svg {{ $attributes->merge(['class'=>$class]) }} viewBox="0 0 24 24" fill="currentColor">
            <path d="M12 17.3l-5.4 3 1-5.9-4.3-4.2 6-.9 2.7-5.4 2.7 5.4 6 .9-4.3 4.2 1 5.9z"/>
        </svg>
    @break
    @case('map')
        <svg {{ $attributes->merge(['class'=>$class]) }} viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6">
            <path d="M9 18l-6 3V6l6-3 6 3 6-3v15l-6 3-6-3z"/><path d="M9 4.5v13"/><path d="M15 6.5v13"/>
        </svg>
    @break
    @case('chart')
        <svg {{ $attributes->merge(['class'=>$class]) }} viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6">
            <path d="M3 3v18h18"/><rect x="7" y="12" width="3" height="6"/><rect x="12" y="8" width="3" height="10"/><rect x="17" y="5" width="3" height="13"/>
        </svg>
    @break
@endswitch
