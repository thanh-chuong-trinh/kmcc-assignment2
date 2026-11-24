<?php $current_page = 'apply'; ?>
<!doctype html>
<!--
  apply.php - Job application form for Second Volt
  Author: Chu Le Gia Khanh (106394068), Group 6 (KMCC), COS10026
  Acknowledgements:
  - OpenAI Codex assisted with the Part 2 form integration and QA (see README.md).
  - Shared header, footer and styles.css conventions by the team (see README.md).
  - process_eoi.php validates submissions and saves them to the project database.
-->
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta
      name="description"
      content="Apply for a role at Second Volt, an online store for tested, used electronics."
    />
    <meta
      name="keywords"
      content="Second Volt, job application, careers, front-end developer, UX designer"
    />
    <meta name="author" content="Chu Le Gia Khanh - 106394068" />
    <meta name="color-scheme" content="light" />
    <title>Apply for a role | SecondVolt Careers</title>
    <link rel="stylesheet" href="styles/style.css" />
    <style>
      /* Small embedded CSS example required by the assignment. */
      #job-reference {
        letter-spacing: 0.12em;
      }
    </style>
  </head>
  <body class="apply-page">
<?php include 'header.inc'; ?>

    <main id="main" class="container apply-main">
      <section class="hero apply-hero" aria-labelledby="page-title">
        <p class="eyebrow">Careers at SecondVolt</p>
        <h1 id="page-title">Apply to <span>SecondVolt.</span></h1>
        <p class="intro-copy">
          Help us give used electronics a second life.
        </p>
      </section>

      <div class="application-layout">
        <aside class="application-sidebar" aria-label="Application guide">
          <nav
            class="section-navigation"
            aria-labelledby="section-navigation-title"
          >
            <h2 id="section-navigation-title">In this application</h2>
            <!-- These are section links, not a progress tracker or a multi-step form. -->
            <ol>
              <li>
                <a href="#position-details"
                  ><span aria-hidden="true">01</span>Position details</a
                >
              </li>
              <li>
                <a href="#personal-details"
                  ><span aria-hidden="true">02</span>Personal details</a
                >
              </li>
              <li>
                <a href="#address-details"
                  ><span aria-hidden="true">03</span>Address details</a
                >
              </li>
              <li>
                <a href="#contact-details"
                  ><span aria-hidden="true">04</span>Contact details</a
                >
              </li>
              <li>
                <a href="#skills-details"
                  ><span aria-hidden="true">05</span>Skills</a
                >
              </li>
            </ol>
          </nav>
          <section
            class="reference-guide"
            aria-labelledby="reference-guide-title"
          >
            <span class="guide-icon" aria-hidden="true">?</span>
            <h2 id="reference-guide-title">Find your job reference</h2>
            <p>
              Read the job descriptions and copy the exact five-character
              reference for your chosen role.
            </p>
            <a class="text-link" href="jobs.php"
              >View job descriptions <span aria-hidden="true">&rarr;</span></a
            >
          </section>
          <p class="sidebar-caption">
            From phones to laptops, help us keep good electronics out of
            landfill.
          </p>
        </aside>

        <div class="form-panel">
          <div class="form-heading">
            <p class="eyebrow">Your application</p>
            <h2 id="form-title">Tell us about yourself</h2>
            <p id="required-note">
              Fields marked
              <span class="required-example">(required)</span> must be
              completed.
            </p>
          </div>
          <div class="demo-notice">
            <span class="notice-icon" aria-hidden="true">i</span>
            <p>
              <strong>Just a coursework demonstration.</strong> Use sample
              details, not personal information.
            </p>
          </div>

          <form
            id="application-form"
            class="application-form"
            tabindex="-1"
            aria-label="Job application"
            aria-describedby="required-note"
            action="process_eoi.php"
            method="post"
            novalidate
          >
            <!-- 1. Position details -->
            <fieldset class="form-section" id="position-details" tabindex="-1">
              <legend>
                <span class="section-number" aria-hidden="true">01</span
                >Position details
              </legend>
              <p class="form-field">
                <label for="job-reference"
                  >Job reference number <span>(required)</span></label
                >
                <input
                  type="text"
                  id="job-reference"
                  name="job_reference"
                  minlength="5"
                  maxlength="5"
                  pattern="SV101|SV205"
                  aria-describedby="job-reference-help job-reference-error"
                  title="Enter SV101 or SV205 exactly as shown on the Jobs page."
                  required
                />
                <small id="job-reference-help"
                  >Enter SV101 or SV205 exactly as shown on the Jobs page.</small
                >
                <span class="field-error" id="job-reference-error"
                  >Error: Enter a valid job reference: SV101 or SV205.</span
                >
              </p>
            </fieldset>

            <!-- 2. Personal details -->
            <fieldset class="form-section" id="personal-details" tabindex="-1">
              <legend>
                <span class="section-number" aria-hidden="true">02</span
                >Personal details
              </legend>
              <div class="form-grid">
                <p class="form-field">
                  <label for="first-name"
                    >First name <span>(required)</span></label
                  >
                  <input
                    type="text"
                    id="first-name"
                    name="first_name"
                    autocomplete="given-name"
                    maxlength="20"
                    pattern="[A-Za-z]{1,20}"
                    aria-describedby="first-name-help first-name-error"
                    title="Enter up to 20 letters from A to Z, without spaces or numbers."
                    required
                  />
                  <small id="first-name-help"
                    >Up to 20 letters (A-Z), as specified for this
                    exercise.</small
                  >
                  <span class="field-error" id="first-name-error"
                    >Error: Enter your first name using 1 to 20 letters (A-Z),
                    without spaces or numbers.</span
                  >
                </p>
                <p class="form-field">
                  <label for="last-name"
                    >Last name <span>(required)</span></label
                  >
                  <input
                    type="text"
                    id="last-name"
                    name="last_name"
                    autocomplete="family-name"
                    maxlength="20"
                    pattern="[A-Za-z]{1,20}"
                    aria-describedby="last-name-help last-name-error"
                    title="Enter up to 20 letters from A to Z, without spaces or numbers."
                    required
                  />
                  <small id="last-name-help"
                    >Up to 20 letters (A-Z), as specified for this
                    exercise.</small
                  >
                  <span class="field-error" id="last-name-error"
                    >Error: Enter your last name using 1 to 20 letters (A-Z),
                    without spaces or numbers.</span
                  >
                </p>
                <p class="form-field field-wide">
                  <label for="date-of-birth"
                    >Date of birth <span>(required)</span></label
                  >
                  <!-- Calendar validity and the current date are checked by PHP. -->
                  <input
                    type="text"
                    id="date-of-birth"
                    name="date_of_birth"
                    autocomplete="bday"
                    placeholder="dd/mm/yyyy"
                    minlength="10"
                    maxlength="10"
                    pattern="[0-9]{2}/[0-9]{2}/[0-9]{4}"
                    aria-describedby="date-of-birth-help date-of-birth-error"
                    title="Enter a real date as dd/mm/yyyy, from 01/01/1900 to today."
                    required
                  />
                  <small id="date-of-birth-help"
                    >Use dd/mm/yyyy, for example 25/03/2004. Accepted range:
                    01/01/1900 to today.</small
                  >
                  <span class="field-error" id="date-of-birth-error"
                    >Error: Enter a real calendar date in dd/mm/yyyy format,
                    from 01/01/1900 to today. Check the day, month and
                    leap year.</span
                  >
                </p>
              </div>

              <fieldset class="choice-group gender-group">
                <legend>Gender <span>(required)</span></legend>
                <div class="choice-options gender-options">
                  <p>
                    <input
                      type="radio"
                      id="gender-female"
                      name="gender"
                      value="female"
                      required
                      aria-describedby="gender-error"
                    />
                    <label for="gender-female">Female</label>
                  </p>
                  <p>
                    <input
                      type="radio"
                      id="gender-male"
                      name="gender"
                      value="male"
                      required
                      aria-describedby="gender-error"
                    />
                    <label for="gender-male">Male</label>
                  </p>
                  <p>
                    <input
                      type="radio"
                      id="gender-non-binary"
                      name="gender"
                      value="non-binary"
                      required
                      aria-describedby="gender-error"
                    />
                    <label for="gender-non-binary">Non-binary</label>
                  </p>
                  <p>
                    <input
                      type="radio"
                      id="gender-not-disclosed"
                      name="gender"
                      value="prefer-not-to-say"
                      required
                      aria-describedby="gender-error"
                    />
                    <label for="gender-not-disclosed">Prefer not to say</label>
                  </p>
                </div>
                <p class="field-error" id="gender-error">
                  Error: Select one gender option. You may choose Prefer not to
                  say.
                </p>
              </fieldset>
            </fieldset>

            <!-- 3. Address details -->
            <fieldset class="form-section" id="address-details" tabindex="-1">
              <legend>
                <span class="section-number" aria-hidden="true">03</span>Address
                details
              </legend>
              <p class="form-field">
                <label for="street-address"
                  >Street address <span>(required)</span></label
                >
                <input
                  type="text"
                  id="street-address"
                  name="street_address"
                  autocomplete="address-line1"
                  maxlength="40"
                  pattern="(?=.*\S).{1,40}"
                  title="Enter up to 40 characters, including at least one non-space character."
                  required
                  aria-describedby="street-address-error"
                />
                <span class="field-error" id="street-address-error"
                  >Error: Enter a street address of up to 40 characters. Spaces
                  alone are not accepted.</span
                >
              </p>
              <div class="form-grid">
                <p class="form-field field-wide">
                  <label for="suburb"
                    >Suburb / Town <span>(required)</span></label
                  >
                  <input
                    type="text"
                    id="suburb"
                    name="suburb"
                    autocomplete="address-level2"
                    maxlength="40"
                    pattern="(?=.*\S).{1,40}"
                    title="Enter up to 40 characters, including at least one non-space character."
                    required
                    aria-describedby="suburb-error"
                  />
                  <span class="field-error" id="suburb-error"
                    >Error: Enter a suburb or town of up to 40 characters.
                    Spaces alone are not accepted.</span
                  >
                </p>
                <p class="form-field">
                  <label for="state"
                    >State / Territory <span>(required)</span></label
                  >
                  <select
                    id="state"
                    name="state"
                    autocomplete="address-level1"
                    required
                    aria-describedby="state-error"
                  >
                    <option value="" selected disabled>
                      Select a state or territory
                    </option>
                    <option value="VIC">VIC</option>
                    <option value="NSW">NSW</option>
                    <option value="QLD">QLD</option>
                    <option value="NT">NT</option>
                    <option value="WA">WA</option>
                    <option value="SA">SA</option>
                    <option value="TAS">TAS</option>
                    <option value="ACT">ACT</option>
                  </select>
                  <span class="field-error" id="state-error"
                    >Error: Select a state or territory.</span
                  >
                </p>
                <p class="form-field">
                  <label for="postcode">Postcode <span>(required)</span></label>
                  <input
                    type="text"
                    id="postcode"
                    name="postcode"
                    inputmode="numeric"
                    autocomplete="postal-code"
                    minlength="4"
                    maxlength="4"
                    pattern="[0-9]{4}"
                    aria-describedby="postcode-help postcode-error"
                    title="Enter exactly four digits."
                    required
                  />
                  <small id="postcode-help"
                    >Exactly four digits, including any leading zero.</small
                  >
                  <span class="field-error" id="postcode-error"
                    >Error: Enter exactly four digits for the postcode.</span
                  >
                </p>
              </div>
            </fieldset>

            <!-- 4. Contact details -->
            <fieldset class="form-section" id="contact-details" tabindex="-1">
              <legend>
                <span class="section-number" aria-hidden="true">04</span>Contact
                details
              </legend>
              <div class="form-grid">
                <p class="form-field">
                  <label for="email">Email <span>(required)</span></label>
                  <input
                    type="email"
                    id="email"
                    name="email"
                    autocomplete="email"
                    required
                    aria-describedby="email-error"
                  />
                  <span class="field-error" id="email-error"
                    >Error: Enter an email address in the format
                    name@example.com.</span
                  >
                </p>
                <p class="form-field">
                  <label for="phone"
                    >Phone number <span>(required)</span></label
                  >
                  <input
                    type="tel"
                    id="phone"
                    name="phone"
                    inputmode="numeric"
                    autocomplete="tel"
                    minlength="8"
                    maxlength="12"
                    pattern="[0-9]{8,12}"
                    aria-describedby="phone-help phone-error"
                    title="Enter 8 to 12 digits without spaces or symbols."
                    required
                  />
                  <small id="phone-help"
                    >8 to 12 digits, without spaces or symbols.</small
                  >
                  <span class="field-error" id="phone-error"
                    >Error: Enter 8 to 12 digits without spaces or
                    symbols.</span
                  >
                </p>
              </div>
            </fieldset>

            <!-- 5. Skills -->
            <fieldset class="form-section" id="skills-details" tabindex="-1">
              <legend>
                <span class="section-number" aria-hidden="true">05</span>Skills
              </legend>
              <p class="role-requirements-intro">
                Read the requirements for the job reference you entered above,
                then complete the three confirmations below.
              </p>
              <div class="role-requirements">
                <section class="role-requirements-card" aria-labelledby="sv101-skills-title">
                  <h3 id="sv101-skills-title">SV101 — Front-end Developer</h3>
                  <ul>
                    <li><strong>Experience:</strong> 3+ years building websites with HTML and CSS.</li>
                    <li><strong>Tools:</strong> Comfortable using Git in a team.</li>
                    <li><strong>Accessibility:</strong> Experience testing pages with a keyboard and a screen reader.</li>
                  </ul>
                </section>
                <section class="role-requirements-card" aria-labelledby="sv205-skills-title">
                  <h3 id="sv205-skills-title">SV205 — UX Designer</h3>
                  <ul>
                    <li><strong>Experience:</strong> 2+ years of UX design experience.</li>
                    <li><strong>Portfolio:</strong> A portfolio that shows your design process.</li>
                    <li><strong>Accessibility:</strong> Working knowledge of WCAG 2.1.</li>
                  </ul>
                </section>
              </div>
              <fieldset
                class="choice-group"
                aria-describedby="skills-help skills-error"
              >
                <legend>Confirm your skills <span>(required)</span></legend>
                <p id="skills-help">
                  Confirm all three requirements for your chosen job reference.
                  You do not need to meet the requirements for the other role.
                </p>
                <!-- Applicants declare the requirements for their selected role.
                     HTML validation checks confirmation, not actual experience. -->
                <div class="choice-options skill-options">
                  <p>
                    <input
                      type="checkbox"
                      id="skill-experience"
                      name="skills[]"
                      value="Required experience for the selected role"
                      required
                      aria-describedby="skills-help skills-error"
                    />
                    <label for="skill-experience">
                      I meet the experience requirement for my chosen role.
                    </label>
                  </p>
                  <p>
                    <input
                      type="checkbox"
                      id="skill-tools-portfolio"
                      name="skills[]"
                      value="Git teamwork or UX portfolio for the selected role"
                      required
                      aria-describedby="skills-help skills-error"
                    />
                    <label for="skill-tools-portfolio">
                      I meet the Git teamwork requirement (SV101) or the
                      portfolio requirement (SV205) for my chosen role.
                    </label>
                  </p>
                  <p>
                    <input
                      type="checkbox"
                      id="skill-accessible-design"
                      name="skills[]"
                      value="Accessibility requirements for the selected role"
                      required
                      aria-describedby="skills-help skills-error"
                    />
                    <label for="skill-accessible-design">
                      I meet the accessibility requirement for my chosen role.
                    </label>
                  </p>
                </div>
                <p class="field-error" id="skills-error">
                  Error: Confirm all three requirements for your chosen role.
                </p>
              </fieldset>

              <p class="form-field">
                <label for="other-skills"
                  >Other skills <span>(optional)</span></label
                >
                <textarea
                  id="other-skills"
                  name="other_skills"
                  rows="5"
                  cols="40"
                  aria-describedby="other-skills-help"
                ></textarea>
                <small id="other-skills-help"
                  >Describe any additional skills relevant to your chosen role.
                  For SV101, you could mention e-commerce experience or an
                  interest in reuse and reducing e-waste. For SV205, you could
                  mention designing for screen reader users or experience with
                  an online store or marketplace.</small
                >
              </p>
            </fieldset>

            <div class="form-bottom">
              <p class="form-test-note">
                Submitting saves your sample application and gives you a
                reference number. This is not a real job application.
              </p>
              <p class="form-actions">
                <!-- Inline CSS example required by the assignment; most styling should remain external. -->
                <button type="submit" style="font-weight: 700">
                  Submit application <span aria-hidden="true">&rarr;</span>
                </button>
              </p>
            </div>
          </form>
        </div>
      </div>
    </main>

<?php include 'footer.inc'; ?>
  </body>
</html>
