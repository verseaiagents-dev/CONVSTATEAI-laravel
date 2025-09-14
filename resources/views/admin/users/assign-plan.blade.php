@extends('layouts.admin')

@section('title', 'Assign Plan to User')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Assign Plan to User</h3>
                    <div class="card-tools">
                        <a href="{{ route('admin.users.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Back to Users
                        </a>
                    </div>
                </div>

                <div class="card-body">
                    <!-- User Information -->
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <h5>User Information</h5>
                            <table class="table table-sm">
                                <tr>
                                    <td><strong>Name:</strong></td>
                                    <td>{{ $user->name }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Email:</strong></td>
                                    <td>{{ $user->email }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Registered:</strong></td>
                                    <td>{{ $user->created_at->format('M d, Y') }}</td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <h5>Current Plan</h5>
                            @if($currentSubscription)
                                <div class="alert alert-info">
                                    <strong>{{ $currentSubscription->plan->name }}</strong><br>
                                    <small>
                                        Started: {{ $currentSubscription->started_at->format('M d, Y') }}<br>
                                        Expires: {{ $currentSubscription->expires_at->format('M d, Y') }}
                                    </small>
                                </div>
                            @else
                                <div class="alert alert-warning">
                                    No active plan assigned
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Plan Assignment Form -->
                    <form action="{{ route('admin.users.assign-plan.store', $user) }}" method="POST">
                        @csrf
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="plan_id">Select Plan *</label>
                                    <select name="plan_id" id="plan_id" class="form-control @error('plan_id') is-invalid @enderror" required>
                                        <option value="">Choose a plan...</option>
                                        @foreach($plans as $plan)
                                            <option value="{{ $plan->id }}" 
                                                    data-tokens="{{ $plan->usage_tokens }}"
                                                    data-price="{{ $plan->price }}"
                                                    {{ old('plan_id') == $plan->id ? 'selected' : '' }}>
                                                {{ $plan->name }} - {{ $plan->usage_tokens }} tokens - {{ $plan->price }} ₺
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('plan_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="start_date">Start Date</label>
                                    <input type="date" name="start_date" id="start_date" 
                                           class="form-control @error('start_date') is-invalid @enderror"
                                           value="{{ old('start_date', now()->format('Y-m-d')) }}">
                                    @error('start_date')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="end_date">End Date</label>
                                    <input type="date" name="end_date" id="end_date" 
                                           class="form-control @error('end_date') is-invalid @enderror"
                                           value="{{ old('end_date', now()->addYear()->format('Y-m-d')) }}">
                                    @error('end_date')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="notes">Notes (Optional)</label>
                                    <textarea name="notes" id="notes" rows="3" 
                                              class="form-control @error('notes') is-invalid @enderror"
                                              placeholder="Add any notes about this plan assignment...">{{ old('notes') }}</textarea>
                                    @error('notes')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Plan Preview -->
                        <div id="plan-preview" class="mt-4" style="display: none;">
                            <div class="card bg-light">
                                <div class="card-header">
                                    <h6 class="mb-0">Plan Preview</h6>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-4">
                                            <strong>Tokens:</strong> <span id="preview-tokens">-</span>
                                        </div>
                                        <div class="col-md-4">
                                            <strong>Price:</strong> <span id="preview-price">-</span> ₺
                                        </div>
                                        <div class="col-md-4">
                                            <strong>Duration:</strong> <span id="preview-duration">-</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="form-group mt-4">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-check"></i> Assign Plan
                            </button>
                            <a href="{{ route('admin.users.index') }}" class="btn btn-secondary ml-2">
                                <i class="fas fa-times"></i> Cancel
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const planSelect = document.getElementById('plan_id');
    const startDate = document.getElementById('start_date');
    const endDate = document.getElementById('end_date');
    const preview = document.getElementById('plan-preview');
    const previewTokens = document.getElementById('preview-tokens');
    const previewPrice = document.getElementById('preview-price');
    const previewDuration = document.getElementById('preview-duration');

    function updatePreview() {
        const selectedOption = planSelect.options[planSelect.selectedIndex];
        if (selectedOption.value) {
            const tokens = selectedOption.dataset.tokens;
            const price = selectedOption.dataset.price;
            const start = startDate.value;
            const end = endDate.value;
            
            previewTokens.textContent = tokens;
            previewPrice.textContent = price;
            
            if (start && end) {
                const startDate = new Date(start);
                const endDate = new Date(end);
                const diffTime = Math.abs(endDate - startDate);
                const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
                previewDuration.textContent = diffDays + ' days';
            }
            
            preview.style.display = 'block';
        } else {
            preview.style.display = 'none';
        }
    }

    planSelect.addEventListener('change', updatePreview);
    startDate.addEventListener('change', updatePreview);
    endDate.addEventListener('change', updatePreview);
    
    // Initial preview
    updatePreview();
});
</script>
@endsection
