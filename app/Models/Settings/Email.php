<?php

namespace App\Models\Settings;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Mail\AppMailer;
use Illuminate\Support\Facades\Mail;


class Email extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'code',
        'file_name',
        'subject',
        'body_html',
        'body_text',
    ];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = [
        'created_at',
        'updated_at',
        'checked_out_time'
    ];


    /*
     * Override.
     */
    public function save(array $options = [])
    {
        // Replace the HTML entities set by the editor in the code placeholders (eg: {{ $data-&gt;name }}).
	$this->body_html = preg_replace('#({{.+)-&gt;(.+}})#', '$1->$2', $this->body_html);

        parent::save($options);

	$this->setViewFiles();
    }

    /*
     * Override.
     */
    public function delete()
    {
        $code = $this->code;

        parent::delete();

	// Delete template files associated with the model.
	unlink(resource_path().'/views/emails/'.$code.'.blade.php');
	unlink(resource_path().'/views/emails/'.$code.'_plain.blade.php');
    }

    /*
     * Creates or updates the template files associated with the model.
     */
    private function setViewFiles()
    {
        // Name the email template after the code attribute.
	$html = resource_path().'/views/emails/'.$this->code.'.blade.php';
	$text = resource_path().'/views/emails/'.$this->code.'_plain.blade.php';

	file_put_contents($html, $this->body_html);
	file_put_contents($text, $this->body_text);
    }

    /*
     * Gets the email items according to the filter, sort and pagination settings.
     */
    public function getItems($request)
    {
        $perPage = $request->input('per_page', General::getGeneralValue('pagination', 'per_page'));
        $search = $request->input('search', null);
        $sortedBy = $request->input('sorted_by', null);

	$query = Email::query();
	$query->select('emails.*', 'users.name as user_name')->leftJoin('users', 'emails.created_by', '=', 'users.id');

	if ($search !== null) {
	    $query->where('code', 'like', '%'.$search.'%');
	}

	if ($sortedBy !== null) {
	    preg_match('#^([a-z0-9_]+)_(asc|desc)$#', $sortedBy, $matches);
	    $query->orderBy($matches[1], $matches[2]);
	}

        return $query->paginate($perPage);
    }

    /*
     * Builds the options for the 'format' select field.
     */
    public function getFormatOptions()
    {
        return [
	    ['value' => 'plain_text', 'text' => 'Plain text'], 
	    ['value' => 'html', 'text' => 'HTML']
	];
    }

    /*
     * Generic function that returns model values which are handled by select inputs. 
     */
    public function getSelectedValue($fieldName)
    {
        if ($fieldName == 'format') {
	    return ($this->plain_text) ? 'plain_text' : 'html';
	}
	else {
	    return $this->{$fieldName};
	}
    }

    /*
     * Checks whether the current user is allowed to access a given email according to their role level.
     *
     * @return boolean
     */
    public function canAccess()
    {
        if ($this->access_level == 'public_ro' || $this->canEdit()) {
	    return true;
	}

	return false;
    }

    /*
     * Checks whether the current user is allowed to edit a given email according to their role level.
     *
     * @return boolean
     */
    public function canEdit()
    {
        if ($this->access_level == 'public_rw' || $this->role_level < auth()->user()->getRoleLevel() || $this->created_by == auth()->user()->id) {
	    return true;
	}

	return false;
    }

    /*
     * Checks whether the current user is allowed to delete a given email according to their role level.
     *
     * @return boolean
     */
    public function canDelete()
    {
        if (auth()->user()->hasRole('super-admin')) {
	    return true;
	}

	// The owner role level is lower than the current user's or the current user owns the email.
	if ($this->role_level < auth()->user()->getRoleLevel() || $this->created_by == auth()->user()->id) {
	    return true;
	}

	return false;
    }

    /*
     * Send an email through a given email template.
     * @param  string  $code
     * @param  Item Instance  data$
     * @return void
     */
    public static function sendEmail($code, $data)
    {
	$email = Email::where('code', $code)->first();
	$data->subject = self::parseSubject($email->subject, $data);
	// Use the email attribute as recipient in case the recipient attribute doesn't exist.
	$recipient = (!isset($data->recipient) && isset($data->email)) ? $data->email : $data->recipient;
	$data->view = 'emails.'.$code;
	Mail::to($recipient)->send(new AppMailer($data));
    }

    /*
     * Replaces the possibles variable set in the email subject with their values.
     * @param  string  $subject
     * @param  Item Instance  data$
     * @return string
     */
    public static function parseSubject($subject, $data)
    {
        if (preg_match_all('#{{\s?\$[a-z0-9_]+\s?}}#U', $subject, $matches)) {
	    $results = $matches[0];
	    $patterns = $replacements = [];

	    foreach ($results as $result) {
		preg_match('#^{{\s?\$([a-zA-Z0-9_]+)\s?}}$#', $result, $matches);
		$attribute  = $matches[1];
		$replacements[] = $data->$attribute;
		$patterns[] = '#({{\s?\$'.$matches[1].'\s?}})#';
	    }

	    return preg_replace($patterns, $replacements, $subject);
	}

	return $subject;
    }
}
