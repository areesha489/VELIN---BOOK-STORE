<div class="form-grid two">
    <label>Label
        <input type="text" name="label" value="{{ old('label', $address->label ?? 'Home') }}" maxlength="40">
    </label>
    <label>Recipient
        <input type="text" name="recipient" required value="{{ old('recipient', $address->recipient ?? auth()->user()->name) }}">
    </label>
    <label>Phone
        <input type="tel" name="phone" required value="{{ old('phone', $address->phone ?? auth()->user()->phone) }}">
    </label>
    <label>Postal code
        <input type="text" name="postal_code" required value="{{ old('postal_code', $address->postal_code ?? '') }}">
    </label>
    <label class="span-2">Address line
        <input type="text" name="line1" required value="{{ old('line1', $address->line1 ?? '') }}">
    </label>
    <label class="span-2">Address line 2
        <input type="text" name="line2" value="{{ old('line2', $address->line2 ?? '') }}">
    </label>
    <label>City
        <input type="text" name="city" required value="{{ old('city', $address->city ?? '') }}">
    </label>
    <label>State
        <input type="text" name="state" required value="{{ old('state', $address->state ?? '') }}">
    </label>
    <label>Country
        <input type="text" name="country" value="{{ old('country', $address->country ?? 'India') }}">
    </label>
    <label class="check">
        <input type="checkbox" name="is_default" value="1" @checked(old('is_default', $address->is_default ?? false))>
        Default address
    </label>
</div>
