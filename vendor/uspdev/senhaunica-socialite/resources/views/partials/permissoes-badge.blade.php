<button data-user-id="{{ $user->id }}" data-route="{{ route('senhaunica-users.show', $user->id) }}"
  class="btn btn-sm btn-{{ $user->labelLevel() }} py-0 senhaunicaUserPermissionBtn" title="Permissão hierárquicas">
  {{ $user->level }}
  {{ $user->env ? '(env)' : '' }}
</button>
