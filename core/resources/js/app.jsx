import React from 'react';
import ReactDOM from 'react-dom/client';
import BookingPage from './components/booking/BookingPage';
import '../css/booking.css';

ReactDOM.createRoot(document.getElementById('booking-root')).render(
    <React.StrictMode>
        <BookingPage />
    </React.StrictMode>
);
