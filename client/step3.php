<?php 
require_once __DIR__ . '/../includes/config.php';

// Check if lawyer was selected
if (!isset($_POST['lawyer']) || !isset($_SESSION['booking']['specialization_id'])) {
    header("Location: index.php");
    exit;
}

$lawyer_id = (int)$_POST['lawyer'];
$_SESSION['booking']['lawyer_id'] = $lawyer_id;

// Get lawyer details
try {
    $stmt = $pdo->prepare("
        SELECT l.*, u.first_name, u.last_name, s.name as specialization_name 
        FROM lawyers l
        JOIN users u ON l.user_id = u.id
        JOIN specializations s ON l.specialization_id = s.id
        WHERE l.id = ?
    ");
    $stmt->execute([$lawyer_id]);
    $lawyer = $stmt->fetch();

    if (!$lawyer) {
        $_SESSION['error'] = "Selected lawyer not found";
        header("Location: step2.php");
        exit;
    }
} catch (PDOException $e) {
    $_SESSION['error'] = "Database error: " . $e->getMessage();
    header("Location: step2.php");
    exit;
}

$pageTitle = "Select Date & Time";
include '../includes/header.php'; 

// Display any errors
if (isset($_SESSION['error'])) {
    echo '<div class="alert alert-danger">' . $_SESSION['error'] . '</div>';
    unset($_SESSION['error']);
}
?>

<div class="container py-5">
    <!-- Progress Steps -->
    <div class="d-flex justify-content-between mb-5">
        <div class="step step-completed rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
            <i class="fas fa-check"></i>
        </div>
        <div class="step step-completed rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
            <i class="fas fa-check"></i>
        </div>
        <div class="step step-active rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
            3
        </div>
        <div class="step bg-light rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
            4
        </div>
    </div>

    <div class="card shadow-sm border-0">
        <div class="card-header bg-white border-0 py-4">
            <div class="text-center">
                <h2 class="mb-1">Select Date & Time</h2>
                <p class="text-muted">Choose a convenient slot for your consultation with</p>
                <div class="d-flex align-items-center justify-content-center">
                    <img src="../assets/images/lawyers/lawyer<?= rand(1,3) ?>.jpg" class="rounded-circle me-3" width="50" height="50">
                    <div>
                        <h5 class="mb-0"><?= htmlspecialchars($lawyer['first_name'] . ' ' . $lawyer['last_name']) ?></h5>
                        <small class="text-muted"><?= htmlspecialchars($lawyer['specialization_name']) ?></small>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="card-body">
            <form action="step4.php" method="post" id="bookingForm">
                <div class="row mb-4">
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Select Your Timezone</label>
                        <select class="form-select" id="timezone" name="timezone" required>
                            <option value="" selected disabled>Choose your timezone</option>
                            <?php
                            $timezones = DateTimeZone::listIdentifiers();
                            foreach ($timezones as $tz):
                                if (strpos($tz, '/') !== false): // Only show region/city timezones
                            ?>
                            <option value="<?= htmlspecialchars($tz) ?>"><?= htmlspecialchars(str_replace('_', ' ', $tz)) ?></option>
                            <?php 
                                endif;
                            endforeach; 
                            ?>
                        </select>
                    </div>
                </div>
                
                <div class="mb-4">
                    <label class="form-label fw-bold">Available Time Slots</label>
                    <div id="calendar" class="border rounded-3 p-3 bg-white shadow-sm"></div>
                    <input type="hidden" id="appointment_date" name="appointment_date" required>
                    <input type="hidden" id="appointment_time" name="appointment_time" required>
                    <div id="selected-slot" class="mt-3 p-3 bg-light rounded d-none">
                        <h6 class="mb-0"><i class="far fa-calendar-check text-primary me-2"></i> <span id="selected-date"></span> at <span id="selected-time"></span></h6>
                    </div>
                </div>
                
                <div class="d-flex justify-content-between mt-5">
                    <a href="step2.php" class="btn btn-outline-secondary px-4 py-2">
                        <i class="fas fa-arrow-left me-2"></i> Back
                    </a>
                    <button type="submit" class="btn btn-primary px-4 py-2" id="nextButton" disabled>
                        Next <i class="fas fa-arrow-right ms-2"></i>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- FullCalendar CSS -->
<link href='https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.css' rel='stylesheet' />
<!-- FullCalendar JS -->
<script src='https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.js'></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const calendarEl = document.getElementById('calendar');
    const timezoneSelect = document.getElementById('timezone');
    const dateInput = document.getElementById('appointment_date');
    const timeInput = document.getElementById('appointment_time');
    const nextButton = document.getElementById('nextButton');
    const selectedSlot = document.getElementById('selected-slot');
    const selectedDate = document.getElementById('selected-date');
    const selectedTime = document.getElementById('selected-time');
    
    let calendar;
    let selectedEvent = null;
    
    timezoneSelect.addEventListener('change', function() {
        if (calendar) {
            calendar.destroy();
        }
        
        const timezone = this.value;
        
        calendar = new FullCalendar.Calendar(calendarEl, {
            initialView: 'timeGridWeek',
            timeZone: timezone,
            headerToolbar: {
                left: 'prev,next today',
                center: 'title',
                right: 'timeGridWeek,timeGridDay'
            },
            slotMinTime: '<?= $lawyer['start_time'] ?>',
            slotMaxTime: '<?= $lawyer['end_time'] ?>',
            slotDuration: '01:00:00',
            slotLabelInterval: '01:00:00',
            allDaySlot: false,
            selectable: true,
            selectMirror: true,
            selectOverlap: false,
            select: function(info) {
                // Clear previous selection
                if (selectedEvent) {
                    selectedEvent.remove();
                }
                
                // Create new selected event
                selectedEvent = new FullCalendar.Event({
                    title: 'Your Appointment',
                    start: info.start,
                    end: info.end,
                    color: '#4f46e5',
                    display: 'background'
                });
                calendar.addEvent(selectedEvent);
                
                // Set hidden inputs
                const selectedDateStr = info.startStr.split('T')[0];
                const selectedTimeStr = info.startStr.split('T')[1].substring(0, 5);
                
                dateInput.value = selectedDateStr;
                timeInput.value = selectedTimeStr;
                
                // Update UI
                displaySelectedSlot(selectedDateStr, selectedTimeStr);
                nextButton.disabled = false;
                
                calendar.unselect();
            },
            events: {
                url: '../includes/functions.php',
                method: 'POST',
                extraParams: {
                    action: 'get_lawyer_availability',
                    lawyer_id: <?= $lawyer_id ?>
                },
                failure: function() {
                    alert('Error loading availability');
                }
            },
            businessHours: {
                daysOfWeek: [<?= $lawyer['available_days'] ?>],
                startTime: '<?= $lawyer['start_time'] ?>',
                endTime: '<?= $lawyer['end_time'] ?>'
            },
            eventColor: '#e5e7eb',
            eventDisplay: 'background',
            slotEventOverlap: false,
            selectAllow: function(selectInfo) {
                // Only allow selection during business hours
                return selectInfo.start.getHours() >= <?= (int)substr($lawyer['start_time'], 0, 2) ?> && 
                       selectInfo.end.getHours() <= <?= (int)substr($lawyer['end_time'], 0, 2) ?>;
            }
        });
        
        calendar.render();
    });
    
    function displaySelectedSlot(dateStr, timeStr) {
        const date = new Date(dateStr);
        const time = new Date('1970-01-01T' + timeStr + 'Z');
        
        selectedDate.textContent = date.toLocaleDateString('en-US', { 
            weekday: 'long', 
            month: 'long', 
            day: 'numeric' 
        });
        
        selectedTime.textContent = time.toLocaleTimeString('en-US', { 
            hour: 'numeric', 
            minute: '2-digit', 
            hour12: true 
        });
        
        selectedSlot.classList.remove('d-none');
    }
    
    document.getElementById('bookingForm').addEventListener('submit', function(e) {
        if (!dateInput.value || !timeInput.value) {
            e.preventDefault();
            alert('Please select a date and time for your appointment');
        }
    });
});
</script>

<style>
.fc .fc-timegrid-slot {
    height: 3em;
}
.fc .fc-timegrid-slot-label {
    vertical-align: middle;
}
.fc .fc-timegrid-col.fc-day-today {
    background-color: #f0fdf4;
}
.fc .fc-timegrid-now-indicator-arrow {
    border-color: #ef4444;
}
.fc .fc-timegrid-now-indicator-line {
    border-color: #ef4444;
}
.fc .fc-timegrid-event-harness-inset .fc-timegrid-event {
    box-shadow: none;
}
.fc .fc-timegrid-event .fc-event-main {
    padding: 2px;
}
.fc .fc-event.booked {
    background-color: #fee2e2;
    border-color: #fee2e2;
}
.fc .fc-event.unavailable {
    background-color: #e5e7eb;
    border-color: #e5e7eb;
}
</style>

<?php include '../includes/footer.php'; ?>