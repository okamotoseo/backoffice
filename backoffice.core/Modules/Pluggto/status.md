Order Status

'pending' - Order is not paid
'partial_payment' - Order is paid, but freight not
'approved' - Order is paid and approved
'waiting_invoice' - Store request to generate Invoice
'invoice_error' - ERP was not able generate the Invoice
'invoiced' - Invoice is done
'shipping_informed' - Store informed the track code and shipped the product
'shipped' - MarketPlace accept the shipping information and informed de customer
'shipping_error' - MarketPlace not accept the shipping information, need to fix the invoice information
'delivered' - Store market as delivered to the customer the product
'canceled' - The order was canceled
'under_review' - The order should not be send, untile the MarketPlace approved again the order

'waiting_picking', // waiting picking :: Aguardando separacao
'processing_picking', //  em separacao
'pending_invoice', // sem nota fiscal
'waiting_invoice', // aguardando nota fiscal
'processing_invoice', // aguardando pagamento fatura
'waiting_expedition', //  aguardando despacho
'processing_expedition', // realizando despacho
'dispatched', // enviado,
'collected' // pickup - objeto foi coletado
                                        
                                        


Status Rules

Order cannot rollback to smaller state (ie. Approved order cannot be mark as Pending, Shipped Order Cannot be Mark as Approved or Pending....
In any time, an Order CAN be mark as Canceled
An Delivered Order can Only be mark as Canceled
Delivery Type

'standard' - Standard Type
'express' - Faster Type
'onehour' - Shipping from Store / Motoboy
'pickup' - Pickup on Store
'fulfillment' - Direct from Marketplace Warehouse
'economy' - Slowly delivery type
'international' - To/From other countries
'scheduled' - With appointment