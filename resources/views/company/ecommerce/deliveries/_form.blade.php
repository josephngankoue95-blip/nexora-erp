<div class="delivery-form-grid">
    <div class="delivery-field full">
        <label for="order_id">Commande *</label>
        <select id="order_id" name="order_id" required>
            <option value="">Sélectionner une commande</option>
            @foreach($orders as $order)
                <option value="{{ $order->id }}" @selected((string)old('order_id', $delivery->order_id) === (string)$order->id)>
                    Commande #{{ $order->id }}{{ isset($order->reference) ? ' — '.$order->reference : '' }}
                </option>
            @endforeach
        </select>
        @error('order_id')<small>{{ $message }}</small>@enderror
    </div>
    <div class="delivery-field"><label for="recipient_name">Nom du destinataire *</label><input id="recipient_name" name="recipient_name" value="{{ old('recipient_name', $delivery->recipient_name) }}" required maxlength="150"></div>
    <div class="delivery-field"><label for="recipient_phone">Téléphone *</label><input id="recipient_phone" name="recipient_phone" value="{{ old('recipient_phone', $delivery->recipient_phone) }}" required maxlength="40"></div>
    <div class="delivery-field"><label for="recipient_email">E-mail</label><input type="email" id="recipient_email" name="recipient_email" value="{{ old('recipient_email', $delivery->recipient_email) }}" maxlength="190"></div>
    <div class="delivery-field"><label for="city">Ville *</label><input id="city" name="city" value="{{ old('city', $delivery->city) }}" required maxlength="120"></div>
    <div class="delivery-field full"><label for="address_line">Adresse complète *</label><input id="address_line" name="address_line" value="{{ old('address_line', $delivery->address_line) }}" required maxlength="255"></div>
    <div class="delivery-field"><label for="zone">Zone / quartier</label><input id="zone" name="zone" value="{{ old('zone', $delivery->zone) }}" maxlength="120"></div>
    <div class="delivery-field"><label for="delivery_fee">Frais de livraison (FCFA) *</label><input type="number" id="delivery_fee" name="delivery_fee" value="{{ old('delivery_fee', $delivery->delivery_fee ?? 0) }}" min="0" step="1" required></div>
    <div class="delivery-field"><label for="carrier_name">Livreur / transporteur</label><input id="carrier_name" name="carrier_name" value="{{ old('carrier_name', $delivery->carrier_name) }}" maxlength="150"></div>
    <div class="delivery-field"><label for="carrier_phone">Téléphone du livreur</label><input id="carrier_phone" name="carrier_phone" value="{{ old('carrier_phone', $delivery->carrier_phone) }}" maxlength="40"></div>
    <div class="delivery-field"><label for="scheduled_at">Date prévue</label><input type="date" id="scheduled_at" name="scheduled_at" value="{{ old('scheduled_at', $delivery->scheduled_at?->format('Y-m-d')) }}"></div>
    <div class="delivery-field"><label for="status">Statut *</label><select id="status" name="status" required>@foreach($statuses as $key => $label)<option value="{{ $key }}" @selected(old('status', $delivery->status ?: 'pending') === $key)>{{ $label }}</option>@endforeach</select></div>
    <div class="delivery-field full"><label for="notes">Notes internes</label><textarea id="notes" name="notes" maxlength="5000">{{ old('notes', $delivery->notes) }}</textarea></div>
</div>
