<?php
require_once __DIR__ . '/../includes/config.php';

if (!isset($_POST['lawyer_id']) || !isset($_SESSION['booking']['specialization_id'])) {
    header("Location: step1.php");
    exit();
}

$lawyer_id = intval($_POST['lawyer_id']);
$specialization_id = $_SESSION['booking']['specialization_id'];

// Store in session for later steps
$_SESSION['booking']['lawyer_id'] = $lawyer_id;

// Fetch lawyer details
$sql = "SELECT l.*, u.first_name, u.last_name, s.name as specialization_name 
        FROM lawyers l
        JOIN users u ON l.user_id = u.id
        JOIN specializations s ON l.specialization_id = s.id
        WHERE l.id = $lawyer_id";
$lawyer = $conn->query($sql)->fetch_assoc();

// Fetch available time slots for this lawyer
$sql = "SELECT * FROM time_slots WHERE lawyer_id = $lawyer_id AND is_available = 1";
$time_slots = $conn->query($sql);

// Fetch existing appointments to mark as unavailable
$sql = "SELECT appointment_date, start_time, end_time FROM appointments 
        WHERE lawyer_id = $lawyer_id AND status = 'confirmed'";
$appointments = $conn->query($sql);

require_once __DIR__ . '/../includes/header.php';
?>

<link href="https://cdn.jsdelivr.net/npm/fullcalendar@5.10.1/main.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.10.1/main.min.js"></script>

<div class="min-h-screen bg-gradient-to-br from-blue-50 to-indigo-100 py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-4xl mx-auto">
        <div class="bg-white shadow-xl rounded-lg overflow-hidden">
            <!-- Progress bar -->
            <div class="px-6 py-4 border-b border-gray-200">
                <div class="flex items-center justify-between">
                    <h2 class="text-xl font-semibold text-gray-800">Select Date & Time</h2>
                    <span class="text-sm font-medium text-indigo-600">Step 3 of 4</span>
                </div>
                <div class="mt-4 w-full bg-gray-200 rounded-full h-2.5">
                    <div class="bg-indigo-600 h-2.5 rounded-full" style="width: 75%"></div>
                </div>
            </div>
            
            <div class="px-6 py-5">
                <div class="bg-indigo-50 border-l-4 border-indigo-400 p-4 mb-6">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-indigo-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2h-1V9z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm text-indigo-700">
                                Booking with <strong><?php echo htmlspecialchars($lawyer['first_name'] . ' ' . $lawyer['last_name']); ?></strong> 
                                for <strong><?php echo htmlspecialchars($lawyer['specialization_name']); ?></strong>.
                            </p>
                        </div>
                    </div>
                </div>
                
                <div id="calendar" class="mb-6 rounded-lg border border-gray-200 p-4"></div>
                
                <form id="timeSlotForm" action="step4.php" method="post">
                    <input type="hidden" name="lawyer_id" value="<?php echo $lawyer_id; ?>">
                    <input type="hidden" name="specialization_id" value="<?php echo $specialization_id; ?>">
                    <input type="hidden" id="selectedDate" name="appointment_date" required>
                    <input type="hidden" id="selectedTime" name="start_time" required>
                    <input type="hidden" id="selectedEndTime" name="end_time" required>
                    
                    <div class="flex justify-between">
                        <a href="step2.php" class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition duration-150 ease-in-out">
                            <svg xmlns="http://www.w3.org/2000/svg" class="-ml-1 mr-2 h-5 w-5 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                            </svg>
                            Back
                        </a>
                        <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition duration-150 ease-in-out" id="nextButton" disabled>
                            Next: Your Details
                            <svg xmlns="http://www.w3.org/2000/svg" class="-mr-1 ml-2 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                            </svg>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const calendarEl = document.getElementById('calendar');
    const selectedDateInput = document.getElementById('selectedDate');
    const selectedTimeInput = document.getElementById('selectedTime');
    const selectedEndTimeInput = document.getElementById('selectedEndTime');
    const nextButton = document.getElementById('nextButton');
    
    // Convert PHP time slots to JavaScript format
    const availableSlots = [
        <?php 
        $time_slots->data_seek(0);
        while($slot = $time_slots->fetch_assoc()): 
            $day = $slot['day_of_week'] - 1; // FullCalendar uses 0-6 for days
            $start = $slot['start_time'];
            $end = $slot['end_time'];
            echo "{ daysOfWeek: [$day], startTime: '$start', endTime: '$end' },\n";
        endwhile; 
        ?>
    ];
    
    // Convert PHP appointments to JavaScript format
    const busySlots = [
        <?php 
        while($appt = $appointments->fetch_assoc()): 
            $date = $appt['appointment_date'];
            $start = $appt['start_time'];
            $end = $appt['end_time'];
            echo "{ start: '$date $start', end: '$date $end', display: 'background', color: '#ff9e9e' },\n";
        endwhile; 
        ?>
    ];
    
    const calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: 'timeGridWeek',
        headerToolbar: {
            left: 'prev,next today',
            center: 'title',
            right: 'timeGridWeek,timeGridDay'
        },
        slotMinTime: '08:00:00',
        slotMaxTime: '18:00:00',
        weekends: false,
        selectable: true,
        selectMirror: true,
        selectOverlap: false,
        selectConstraint: {
            daysOfWeek: [1, 2, 3, 4, 5], // Mon-Fri
            startTime: '09:00',
            endTime: '17:00'
        },
        businessHours: availableSlots,
        events: busySlots,
        select: function(info) {
            const start = info.start;
            const end = info.end;
            
            // Check if selected slot is within business hours
            const isAvailable = availableSlots.some(slot => {
                const dayMatch = slot.daysOfWeek.includes(start.getDay());
                const startTime = slot.startTime.split(':');
                const endTime = slot.endTime.split(':');
                const slotStart = new Date(start);
                slotStart.setHours(parseInt(startTime[0]), parseInt(startTime[1]), 0);
                const slotEnd = new Date(start);
                slotEnd.setHours(parseInt(endTime[0]), parseInt(endTime[1]), 0);
                
                return dayMatch && start >= slotStart && end <= slotEnd;
            });
            
            if (!isAvailable) {
                alert('Please select an available time slot during business hours.');
                calendar.unselect();
                return;
            }
            
            // Check if selected slot conflicts with existing appointments
            const isBusy = busySlots.some(slot => {
                const busyStart = new Date(slot.start);
                const busyEnd = new Date(slot.end);
                return (start < busyEnd && end > busyStart);
            });
            
            if (isBusy) {
                alert('This time slot is already booked. Please select another time.');
                calendar.unselect();
                return;
            }
            
            // Format date and time for form submission
            const dateStr = start.toISOString().split('T')[0];
            const timeStr = start.toTimeString().substring(0, 5);
            const endTimeStr = end.toTimeString().substring(0, 5);
            
            selectedDateInput.value = dateStr;
            selectedTimeInput.value = timeStr + ':00';
            selectedEndTimeInput.value = endTimeStr + ':00';
            
            nextButton.disabled = false;
            
            // Show selected time confirmation
            const toast = document.createElement('div');
            toast.className = 'fixed bottom-4 right-4 bg-indigo-600 text-white px-4 py-2 rounded-md shadow-lg flex items-center';
            toast.innerHTML = `
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                </svg>
                Selected: ${dateStr} from ${timeStr} to ${endTimeStr}
            `;
            document.body.appendChild(toast);
            
            setTimeout(() => {
                toast.classList.add('opacity-0', 'transition', 'duration-300', 'ease-in-out');
                setTimeout(() => toast.remove(), 300);
            }, 3000);
        }
    });
    
    calendar.render();
});
</script>

<?php
require_once __DIR__ . '/../includes/footer.php';
?>