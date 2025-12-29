<form method="POST" action="{{ route('admin.orders.updateStatus', $order->id) }}">
    @csrf
    @method('PATCH')
    <select name="status">
        <option value="pending" {{ $order->status === 'pending' ? 'selected' : '' }}>Pending</option>
        <option value="processing" {{ $order->status === 'processing' ? 'selected' : '' }}>Processing</option>
        <option value="shipped" {{ $order->status === 'shipped' ? 'selected' : '' }}>Shipped</option>
        <option value="completed" {{ $order->status === 'completed' ? 'selected' : '' }}>Completed</option>
        <option value="paid" {{ $order->status === 'paid' ? 'selected' : '' }}>Paid</option>
        <option value="cancelled" {{ $order->status === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
    </select>
    <button type="submit">Mentés</button>
</form>
