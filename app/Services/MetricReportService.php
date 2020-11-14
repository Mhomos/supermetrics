<?php
namespace App\Services;

use App\Helpers\HelperMethods;
use App\Models\Post;
use Carbon\Carbon;
use Tightenco\Collect\Support\Collection;

class MetricReportService
{
    /**
     * Current Generated Report
     *
     * @var
     */
    private $report;

    /**
     * @var array
     */
    private $posts;

    /**
     * @var SupermetricsSocialNetworkService
     */
    private $supermetricsService;

    /**
     * @var mixed
     */
    private $result;

    private $resultData = [];
    private $page;


    /**
     * MetricReportService constructor.
     *
     */
    public function __construct()
    {
        $this->supermetricsService = new SupermetricsSocialNetworkService();
    }

    /**
     * Initiate the Report Key
     *
     * @param $report
     * @return $this
     */
    public function setReport($report)
    {
        $this->report = $report;

        return $this;
    }

    /**
     * Validate the Report
     *
     * @return $this
     */
    public function validateReport()
    {
        if (!in_array($this->report, array_column(HelperMethods::config('reports'), 'key'))) {
            exit(sprintf('The report <<< %s >>> can not be found!!', $this->report));
        }

        return $this;
    }

    /**
     * Get the generated data
     *
     * @return mixed
     */
    public function getResult()
    {
        return $this->result;
    }

    /**
     * Fetch the data and generate the report
     *
     * @return $this
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function generateReport()
    {
        $pageNumber = 1; // start from pageOne
        while ($this->supermetricsService->pageNumber <= HelperMethods::config('max_page_number')) {
            $service = $this->supermetricsService->setPageNumber($pageNumber)->fetchPosts(); // Fetch First 100 post
            $posts = $this->mapPosts($service->getPosts()); // map posts to Post Model
            $this->{$this->getReportMethodName()}($posts); // Manipulating the 100 post by the report logic
            $this->page = $pageNumber;
            $pageNumber++; // increase number of pages
        }; // stop at the max end page

        return $this;
    }

    /**
     * Generate Report Method
     * I: avg_ch_length_per_month => O: avgChLengthPerMonth
     *
     * @return string
     */
    private function getReportMethodName()
    {
        $method = HelperMethods::toCamelCase($this->report . "_report");

        $this->checkReportMethodExists($method);

        return $method;
    }

    /**
     * Check if report method implementation exists
     * @param $method
     */
    private function checkReportMethodExists($method)
    {
        if (!method_exists($this, $method)) {
            exit(sprintf('The report method <<< %s >>> can not be found!!', $method));
        }
    }

    /**
     * Average = Sum of Message Length / Total Number of Posts
     *
     * @param Collection $posts
     */
    private function avgChLengthPerMonthReport(Collection $posts)
    {
        $posts->groupBy('month')->each(function ($postsPerMonth, $month) {
            $sumMessageLength = $postsPerMonth->sum('message_length');
            $total_posts = $postsPerMonth->count();

            $this->resultData[$month] = [
                'month' => $month,
                'month_title' => Carbon::create()->month($month)->format('F'),
                'sum_message_length' => isset($this->resultData[$month]) ? ($this->resultData[$month]['sum_message_length'] + $sumMessageLength) : $sumMessageLength,
                'total_posts' => isset($this->resultData[$month]) ? ($this->resultData[$month]['total_posts'] + $total_posts) : $total_posts,
            ];

            $average = round($this->resultData[$month]['sum_message_length'] / $this->resultData[$month]['total_posts']);
            $this->resultData[$month]['average'] = $average;
        });

        $this->result = [
            'description' => $this->getReportDescription($this->report),
            'data' => collect($this->resultData)->sortBy('month')->values(),
        ];
    }

    /**
     * Longest post by character length per month
     *
     * @param Collection $posts
     */
    private function longestChLengthPerMonthReport(Collection $posts)
    {
        $posts->groupBy('month')->each(function ($postsPerMonth, $month) {
            $maxPostLength = $postsPerMonth->max('message_length');
            $post = $postsPerMonth->where('message_length', $maxPostLength)->first();

            $post = (isset($this->resultData[$month]) && ($maxPostLength > $this->resultData[$month]['max_post_length'])) ? $post : ($this->resultData[$month]['post'] ?? $post);

            $this->resultData[$month] = [
                'month' => $month,
                'month_title' => Carbon::create()->month($month)->format('F'),
                'max_post_length' => (isset($this->resultData[$month]) && ($maxPostLength > $this->resultData[$month]['max_post_length'])) ? $maxPostLength : ($this->resultData[$month]['max_post_length'] ?? $maxPostLength),
                'post' => $post,
            ];
        });

        $this->result = [
            'description' => $this->getReportDescription($this->report),
            'data' => collect($this->resultData)->sortBy('month')->values(),
        ];
    }

    /**
     * Longest post by character length per month
     *
     * @param Collection $posts
     */
    private function totalPerWeekReport(Collection $posts)
    {
        $posts->groupBy('week')->each(function ($postsPerWeekNumber, $weekNumber) {
            $total_posts = $postsPerWeekNumber->count();

            $this->resultData[$weekNumber] = [
                'week_number' => $weekNumber,
                'total_posts' => isset($this->resultData[$weekNumber]) ? ($this->resultData[$weekNumber]['total_posts'] + $total_posts) : $total_posts,
            ];
        });

        $this->result = [
            'description' => $this->getReportDescription($this->report),
            'data' => collect($this->resultData)->sortBy('month')->values(),
        ];
    }

    private function avgPerUserPerMonthReport(Collection $posts)
    {
        $posts->groupBy('from_id')->each(function ($postsPerUser, $userId) {
            $postsPerUser->groupBy('month')->each(function ($postsPerMonth, $month) use ($userId) {
                $sumMessageLength = $postsPerMonth->sum('message_length');
                $total_posts = $postsPerMonth->count();

                $this->resultData[$userId][$month] = [
                    'from_id' => $userId,
                    'month' => $month,
                    'month_title' => Carbon::create()->month($month)->format('F'),
                    'sum_message_length' => isset($this->resultData[$userId][$month]) ? ($this->resultData[$userId][$month]['sum_message_length'] + $sumMessageLength) : $sumMessageLength,
                    'total_posts' => isset($this->resultData[$userId][$month]) ? ($this->resultData[$userId][$month]['total_posts'] + $total_posts) : $total_posts,
                ];

                $average = round($this->resultData[$userId][$month]['sum_message_length'] / $this->resultData[$userId][$month]['total_posts']);
                $this->resultData[$userId][$month]['average'] = $average;
            });

        });

        $this->result = [
            'description' => $this->getReportDescription($this->report),
            'data' => collect($this->resultData)->sortBy('month')->values(),
        ];
    }

    /**
     * Map Posts to Post Model
     *
     * @param $posts
     * @return array
     */
    private function mapPosts($posts)
    {
        $array_map = [];
        foreach ($posts as $key => $post) {
            $post = new Post($post);
            $array_map[$key] = new Post($post);
        }

        return collect($array_map);
    }

    /**
     * Get Report Description
     *
     * @param $report
     * @return mixed
     */
    private function getReportDescription($report)
    {
        $report = collect(HelperMethods::config('reports'))->where('key', $report)->first();

        return $report['description'];
    }


}
