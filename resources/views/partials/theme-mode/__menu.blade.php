<set-theme 
current-mode="{{ Auth::user()->theme_mode }}"
submit-url="{{ route("users.update-theme", Auth::user()->id) }}"
/>