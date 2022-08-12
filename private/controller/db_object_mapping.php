<?php 
// Object–relational mapping (ORM)
class DatabaseObject
{

    public function __construct(object $fetchObj, array $subComponnets = null)
    {
        // load all object's default database column properties
        foreach ($fetchObj as $key => $value) {
            $this->$key = $value;
        }

        $this->subComponents = $subComponnets;
    }

    public function get_instances(string $instance_class): Generator {

        if ($this instanceof $instance_class) {
            yield $this;
        } else {
            foreach ($this->subComponents as $child) {
                yield from $child->get_instances($instance_class);
            }
        }
    }

    public function get_work_seconds(Session $session, bool $display_net_time): int {
        return $display_net_time ? $session->net_time : $session->gross_time;
    }


    public function get_sessions_work(array $sessions, bool $display_net_time): int {
        $seconds = 0;

        foreach ($sessions as $session) {
            $seconds += $this->get_work_seconds($session, $display_net_time);
        }
      
        return $seconds;
    }

    public function get_latest_sessions_work(array $sessions, bool $display_net_time, string $time_frame = '-7 days'): int {
        $seconds = 0;

        foreach ($sessions as $session) {
            $session_date = new DateTime($session->date_created);
            $limit = new DateTime($time_frame);

            if ($session_date > $limit) {
                $seconds += $this->get_work_seconds($session, $display_net_time);
            }
        }
        return $seconds;
    }

    public function get_unpaid_work(array $sessions, bool $display_net_time): int {
      $seconds = 0;

        if ($display_net_time) {
            foreach ($sessions as $session) {
                $seconds += (int) $session->net_checkout_time;
            }
        } else {
            foreach ($sessions as $session) {
                $seconds += (int) $session->gross_checkout_time;
            }
        }
        return $seconds;
    }

    // calculate salary based on working-time (2 decimals)
    public function get_salary(int $work_seconds, float $salary_rate): float {
        return round(($work_seconds / 60 / 60) * $salary_rate, 2);
    }

    // effectivity net_time_ratio (2 decimals)
    public function get_net_ratio(array $sessions): string {
        if (!$sessions) return '';

        $ratios = 0;

        foreach ($sessions as $session) {
            $ratios += $session->net_time_ratio;
        }

        return (string) round($ratios*100 / count($sessions), 2) . '%';
    }
}

class Group extends DatabaseObject
{
    public string $primary_gradient_second_color;
    public string $text_color;

    public function __construct(object $fetchObj, array $subComponnets)
    {
        parent::__construct($fetchObj, $subComponnets);

        $this->primary_gradient_second_color = Format::generate_secondary_gradient_clr($this->primary_color);
        $this->text_color = Format::get_contrast_clr($this->primary_color);
    }

    public function last_session_activity(): string
    {
        $session_dates = [];

        foreach ($this->subComponents as $project) {
            foreach ($project->subComponents as $task) {
                foreach ($task->subComponents as $session) {
                    $session_dates[] = $session->date_created;
                }
            }
        }

        if ($session_dates) {
            $max = Date('Y-m-d H:i:s', max(array_map('strtotime', $session_dates)));
            return Format::last_activity($max);
        } else {
            return '';
        }
    }
}

class Project extends DatabaseObject {}

class Task extends DatabaseObject {}

class Session extends DatabaseObject {}